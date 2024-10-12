<?php

namespace hesabro\hris\models;

use hesabro\helpers\behaviors\JsonAdditional;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\hris\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property int $type
 * @property array $additional_data
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int|null $deleted_at
 * @property int $slave_id
 *
 * @property-read object $user
 * @property-read EmployeeBranch $branch
 * @property-read ContractTemplates $contractTemplate
 * @property-read object $indicator
 *
 * @mixin LogBehavior
 * @mixin TimestampBehavior
 * @mixin BlameableBehavior
 * @mixin JsonAdditional
 * @mixin SoftDeleteBehavior
 */
class EmployeeRequestBase extends ActiveRecord
{
    // Configs

    const SCENARIO_CREATE_OFFICIAL_LETTER = 'create_official_letter';

    const SCENARIO_UPDATE_OFFICIAL_LETTER = 'update_official_letter';

    const SCENARIO_DELETE_OFFICIAL_LETTER = 'update_official_letter';

    const STATUS_PENDING = 0;

    const STATUS_ACCEPT = 1;

    const STATUS_REJECT = 2;

    const TYPE_LETTER = 1;

    public mixed $contract_template_id = null;

    public mixed $description = null;

    public mixed $reject_description = null;

    public mixed $indicator_id = null;

    public static function tableName()
    {
        return '{{%employee_requests}}';
    }

    public static function find(): EmployeeRequestQuery
    {
        return (new EmployeeRequestQuery(get_called_class()))->notDeleted();
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by'
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'contract_template_id' => 'Integer',
                    'description' => 'String',
                    'reject_description' => 'String',
                    'indicator_id' => 'Integer'
                ],
            ],
            [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => time(),
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => null,
                ],
                'replaceRegularDelete' => false,
                'invokeDeleteEvents' => false
            ]
        ]);
    }

    public function rules(): array
    {
        return [
            ['id', 'canUpdate', 'on' => [self::SCENARIO_UPDATE_OFFICIAL_LETTER]],
            ['id', 'canDelete', 'on' => [self::SCENARIO_DELETE_OFFICIAL_LETTER]],
            [['user_id', 'branch_id', 'type', 'status', 'contract_template_id'], 'required'],
            ['user_id', 'exist', 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['user_id' => 'id']],
            ['branch_id', 'exist', 'targetClass' => EmployeeBranch::class, 'targetAttribute' => ['branch_id' => 'id']],
            ['contract_template_id', 'exist', 'targetClass' => ContractTemplates::class, 'targetAttribute' => ['contract_template_id' => 'id']],
            ['type', 'in', 'range' => [self::TYPE_LETTER]],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_REJECT, self::STATUS_ACCEPT]],
        ];
    }

    public function scenarios()
    {

        return [
            self::SCENARIO_CREATE_OFFICIAL_LETTER => ['contract_template_id', 'description'],
            self::SCENARIO_UPDATE_OFFICIAL_LETTER => ['contract_template_id', 'description'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => Module::t('module', 'ID'),
            'user_id' => Module::t('module', 'User'),
            'type' => Module::t('module', 'Category'),
            'branch_id' => Module::t('module', 'Branch'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'updated_at' => Module::t('module', 'Updated At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave'),
            'contract_template_id' => Module::t('module', 'Letter'),
            'description' => Module::t('module', 'Description'),
            'reject_description' => Module::t('module', 'Reject Description'),
            'indicator_id' => Module::t('module', 'Indicator'),
        ];
    }

    // Relations

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
    }

    public function getBranch(): ActiveQuery
    {
        return $this->hasOne(EmployeeBranch::class, ['id' => 'branch_id']);
    }

    public function getContractTemplate(): ActiveQuery
    {
        return $this->hasOne(ContractTemplates::class, ['id' => 'contract_template_id']);
    }

    // Validators

    public function currentUserIsOwner(): bool
    {
        return Yii::$app->user?->id === $this->user_id;
    }

    public function canCreate(): bool
    {
        /** @var EmployeeRequest $previousRequest */
        $previousRequest = self::find()
            ->andWhere(['status' => self::STATUS_PENDING])
            ->andWhere(['type' => $this->type])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (
            $previousRequest &&
            $previousRequest->type === self::TYPE_LETTER &&
            $previousRequest->contract_template_id === $this->contract_template_id
        ) {
            return false;
        }

        return true;
    }

    public function canConfirm(): bool
    {
        if ($this->status != self::STATUS_PENDING) {
            return false;
        }

        if (!EmployeeBranchUser::find()->andWhere(['user_id' => $this->user_id])->exists()) {
            return false;
        }

        return true;
    }

    public function canReject(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function canUpdate(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function canDelete(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    // Aliases

    public static function itemAlias($type, $code = NULL): string|array|bool
    {
        $items = [
            'Type' => [
                self::TYPE_LETTER => Module::t('module', 'Letter')
            ],
            'Status' => [
                self::STATUS_PENDING => Module::t('module', 'Wait Confirm'),
                self::STATUS_REJECT => Module::t('module', 'Rejected'),
                self::STATUS_ACCEPT => Module::t('module', 'Confirmed'),
            ],
            'StatusClass' => [
                self::STATUS_PENDING => 'warning',
                self::STATUS_ACCEPT => 'success',
                self::STATUS_REJECT => 'danger'
            ],
            'StatusIcon' => [
                self::STATUS_PENDING => 'ph:clock-countdown-duotone',
                self::STATUS_ACCEPT => 'ph:check-circle-duotone',
                self::STATUS_REJECT => 'ph:x-circle-duotone'
            ]
        ];

        return isset($code) ? ($items[$type][$code] ?? false) : ($items[$type] ?? false);
    }

    // Actions

    public function confirm(): bool
    {
        $this->status = self::STATUS_ACCEPT;
        return $this->save(false);
    }

    public function reject(): bool
    {
        $this->status = self::STATUS_REJECT;
        return $this->save(false);
    }

    public function pending(): bool
    {
        $this->status = self::STATUS_PENDING;
        return $this->save(false);
    }
}