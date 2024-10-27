<?php

namespace hesabro\hris\controllers;

use hesabro\hris\models\AdvanceMoney;
use hesabro\hris\models\Comfort;
use hesabro\hris\models\ComfortItems;
use hesabro\hris\models\ComfortSearch;
use hesabro\hris\models\EmployeeContent;
use hesabro\hris\models\EmployeeBranchUser;
use hesabro\hris\models\EmployeeRequest;
use hesabro\hris\models\EmployeeRollCallSearch;
use hesabro\hris\models\RequestLeave;
use hesabro\hris\models\SalaryItemsAddition;
use hesabro\hris\models\SalaryItemsAdditionSearch;
use hesabro\hris\models\SalaryPeriod;
use hesabro\hris\models\SalaryPeriodItems;
use hesabro\hris\models\SalaryPeriodItemsSearch;
use hesabro\hris\models\UserContractsSearch;
use hesabro\hris\Module;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class EmployeeProfileController extends EmployeeProfileBase
{
    public function actionIndex($year = null, $month = null)
    {
        $userId = Yii::$app->user->getId();
        $employee = EmployeeBranchUser::findOne(['user_id' => $userId]);
        $time = time();
        $bannersQuery = EmployeeContent::find()
            ->byCurrentClientAccess()
            ->byCustomUserId($userId)
            ->byIsBanner(true)
            ->byShowStartAt($time)
            ->byShowEndAt($time);

        if ($jobCode = $employee?->job_code) {
            $bannersQuery->byCustomJobTags([$jobCode]);
        }

        $banners = $bannersQuery->all();
        $requestLeave = RequestLeave::find()->my($userId)->select(['type', 'status', 'from_date'])->orderBy(['id' => SORT_DESC])->limit(2)->all();
        $advanceMoney = AdvanceMoney::find()->my($userId)->select(['status', 'amount', 'created'])->orderBy(['id' => SORT_DESC])->limit(2)->all();
        $comfortItems = ComfortItems::find()->byUser($userId)->select(['comfort_id', 'status', 'created'])->with(['comfort'])->orderBy(['id' => SORT_DESC])->limit(2)->all();
        $employeeRequests = $employee ? EmployeeRequest::find()->byEmployee($employee)->select(['type', 'status', 'created_at'])->orderBy(['id' => SORT_DESC])->limit(2)->all() : [];

        $requestLeave = array_map(fn (RequestLeave $requestLeave) => ([
            'title' => RequestLeave::itemAlias('Types', $requestLeave->type),
            'status' => RequestLeave::itemAlias('Status', $requestLeave->status),
            'icon' => RequestLeave::itemAlias('StatusIcon', $requestLeave->status),
            'color' => RequestLeave::itemAlias('StatusClass', $requestLeave->status),
            'date' => $requestLeave->from_date
        ]), $requestLeave);

        $advanceMoney = array_map(fn (AdvanceMoney $advanceMoney) => ([
            'title' => Module::t('module', 'Advance Money'),
            'status' => AdvanceMoney::itemAlias('Status', $advanceMoney->status),
            'icon' => AdvanceMoney::itemAlias('StatusIcon', $advanceMoney->status),
            'color' => AdvanceMoney::itemAlias('StatusClass', $advanceMoney->status),
            'date' => $advanceMoney->created
        ]), $advanceMoney);

        $comfortItems = array_map(fn (ComfortItems $comfortItem) => ([
            'title' => $comfortItem->comfort->title,
            'status' => ComfortItems::itemAlias('Status', $comfortItem->status),
            'icon' => ComfortItems::itemAlias('StatusIcon', $comfortItem->status),
            'color' => ComfortItems::itemAlias('StatusClass', $comfortItem->status),
            'date' => $comfortItem->created
        ]), $comfortItems);

        $employeeRequests = array_map(fn (EmployeeRequest $employeeRequest) => ([
            'title' => EmployeeRequest::itemAlias('Type', $employeeRequest->type),
            'status' => EmployeeRequest::itemAlias('Status', $employeeRequest->status),
            'icon' => EmployeeRequest::itemAlias('StatusIcon', $employeeRequest->status),
            'color' => EmployeeRequest::itemAlias('StatusClass', $employeeRequest->status),
            'date' => $employeeRequest->created_at
        ]), $employeeRequests);

        $comfortModel = new ComfortSearch();
        /** @var Comfort[] $loans */
        $comforts = $employee ? $comfortModel->searchUser([], $employee)->query->orderBy(['id' => SORT_DESC])->limit(10)->all() : [];

        $workTime = $employee ? (new SalaryPeriodItemsSearch())->totalWorkByUser($userId) : 0;

        $salaryItemsAdditionSearchFormName = (new SalaryItemsAdditionSearch())->formName();

        $yearText = Yii::$app->jdf::jdate('Y');
        $monthText = Yii::$app->jdf::jdate('F');
        $monthValue = (int) Yii::$app->jdf::jdate('m');

        $yearOffset = 5;
        $year = (int) $year;
        $month = (int) $month;
        $currentMonth = Yii::$app->jdf::getStartAndEndOfCurrentMonth();
        $currentYear = Yii::$app->jdf::getStartAndEndOfCurrentYear();

        if ($year && $year <= $yearText && $year >= ($yearText - $yearOffset)) {
            $thisYear = Yii::$app->jdf::jalaliToTimestamp(Yii::$app->jdf::jdate("$year/01/01"), 'Y/m/d');
            $nextYear = Yii::$app->jdf::jalaliToTimestamp(($year + 1) . '/01/01', 'Y/m/d');
            $yearText = (string) $year;
            $currentYear = ['start' => $thisYear, 'end' => $nextYear];
        }

        if ($month && $month >= 1 && $month <= 12) {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            $month = Yii::$app->jdf::jdate("$yearText/$month/01");
            $nextMonth = Yii::$app->jdf::jalaliToTimestamp(Yii::$app->jdf::nextMonth($month) . '/01', 'Y/m/d');
            $month = Yii::$app->jdf::jalaliToTimestamp($month, 'Y/m/d');
            $monthText = Yii::$app->jdf::jdate('F', $month);
            $monthValue = (int) Yii::$app->jdf::jdate('m', $month);
            $currentMonth = [$month, $nextMonth];
        }

        $monthOverTimeDayReport = $employee ? (new SalaryItemsAdditionSearch())->searchReport([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'kind' => SalaryItemsAddition::KIND_OVER_TIME,
                'type' => SalaryItemsAddition::TYPE_OVER_TIME_DAY,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[0]),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[1])
            ]
        ])->query->one() : null;

        $monthOverTimeNightReport = $employee ? (new SalaryItemsAdditionSearch())->searchReport([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'kind' => SalaryItemsAddition::KIND_OVER_TIME,
                'type' => SalaryItemsAddition::TYPE_OVER_TIME_NIGHT,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[0]),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[1])
            ]
        ])->query->one() : null;

        $monthOverTimeHolidayReport = $employee ? (new SalaryItemsAdditionSearch())->searchReport([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'kind' => SalaryItemsAddition::KIND_OVER_TIME,
                'type' => SalaryItemsAddition::TYPE_OVER_TIME_HOLIDAY,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[0]),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[1])
            ]
        ])->query->one() : null;

        $monthLowTime = $employee ? (new SalaryItemsAdditionSearch())->searchReport([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'kind' => SalaryItemsAddition::KIND_LOW_TIME,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[0]),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[1])
            ]
        ])->query->one() : null;

        $monthLeaveReport = $employee ? (new SalaryItemsAdditionSearch())->searchReportLeave([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[0]),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentMonth[1])
            ]
        ])->query->one() : null;

        $yearLeaveReport = $employee ? (new SalaryItemsAdditionSearch())->searchReportLeave([
            $salaryItemsAdditionSearchFormName => [
                'user_id' => $userId,
                'from_date' => Yii::$app->jdf->jdate('Y/m/d', $currentYear['start']),
                'to_date' => Yii::$app->jdf->jdate('Y/m/d', $currentYear['end'])
            ]
        ])->query->one() : null;

        $yearLeaveLimit = 26;
        $monthLeaveLimit = 2.15625;
        $yearLeaveRemain = max($yearLeaveLimit - $yearLeaveReport?->total ?: 0, 0);
        $monthLeaveRemain = max($monthLeaveLimit - $monthLeaveReport?->total ?: 0, 0);

        $lastRequests = [...$requestLeave, ...$advanceMoney, ...$comfortItems, ...$employeeRequests];
        usort($lastRequests, fn ($a, $b) => $a['date'] === $b['date'] ? 0 : (($b['date'] < $a['date']) ? -1 : 1));
        return $this->render('index', [
            'user' => Module::getInstance()->user::findOne($userId),
            'employee' => $employee,
            'workTime' => $workTime,
            'overTimeDay' => $monthOverTimeDayReport?->total ?: 0,
            'overTimeNight' => $monthOverTimeNightReport?->total ?: 0,
            'overTimeHoliday' => $monthOverTimeHolidayReport?->total ?: 0,
            'lowTime' => $monthLowTime?->total ?: 0,
            'monthLeaveLimit' => $monthLeaveLimit,
            'monthLeaveRemain' => $monthLeaveRemain,
            'monthLeaveTotal' => $monthLeaveReport?->total ?: 0,
            'yearLeaveLimit' => $yearLeaveLimit,
            'yearLeaveRemain' => $yearLeaveRemain,
            'yearLeaveTotal' => $yearLeaveReport?->total ?: 0,
            'comforts' => $comforts,
            'lastRequests' => $lastRequests,
            'yearText' => $yearText,
            'monthValue' => $monthValue,
            'monthText' => $monthText,
            'banners' => $banners
        ]);
    }

    public function actionRollCall()
    {
        $searchModel = new EmployeeRollCallSearch();
        $startAndEndOfCurrentMonth = Yii::$app->jdf::getStartAndEndOfCurrentMonth();
        $start_month = Yii::$app->jdf::jdate('Y/m/d', $startAndEndOfCurrentMonth[0]);
        $end_month = Yii::$app->jdf::jdate('Y/m/d', $startAndEndOfCurrentMonth[1]);
        $searchModel->fromDate = $start_month;
        $searchModel->toDate = $end_month;
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('roll-call', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionContracts()
    {
        $searchModel = new UserContractsSearch(['user_id' => Yii::$app->user->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('contracts', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSalaryPeriod()
    {
        $searchModel = new SalaryPeriodItemsSearch(['user_id' => Yii::$app->user->id]);
        $dataProvider = $searchModel->searchUser(Yii::$app->request->queryParams);
        $dataProvider->query
            ->joinWith('period')
            ->andWhere([SalaryPeriod::tableName() . '.status' => SalaryPeriod::STATUS_PAYMENT])
            ->andWhere([SalaryPeriodItems::tableName() . '.can_payment' => Yii::$app->helper::CHECKED])
            ->andWhere(['user_id' => Yii::$app->user->id]);

        return $this->render('salary-period', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewSingleItem($id): string
    {
        $salaryPeriodItem = $this->findModelSalaryItem($id);
        if (!$salaryPeriodItem->canPrint()) {
            throw new ForbiddenHttpException('امکان مشاهده وجود ندارد');
        }
        return $this->render('view-single-item', [
            'model' => $salaryPeriodItem,
        ]);
    }

    public function actionPrintSingleItem($id): string
    {
        $this->layout = '@backend/views/layouts/print';
        $salaryPeriodItem = $this->findModelSalaryItem($id);
        if (!$salaryPeriodItem->canPrint()) {
            throw new ForbiddenHttpException('امکان چاپ وجود ندارد');
        }
        return $this->render('print-single-item', [
            'model' => $salaryPeriodItem,
        ]);
    }

    protected function findModelSalaryItem($id): ?SalaryPeriodItems
    {
        if (($model = SalaryPeriodItems::findOne($id)) !== null && $model->user_id == Yii::$app->user->id) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
