function calculateSalary() {
    let $hours_of_work = parseInt($("#salaryperioditems-hours_of_work").val());
    let $countDay = parseInt($("#salaryperioditems-hours_of_work").data('count-day'));
    let $isManager = parseInt($("#salaryperioditems-hours_of_work").data('manager'));

    calculateCostOfChildren();
    let $basic_salary = ChangeStr($("#salaryperioditems-basic_salary").val());
    let $cost_of_children = ChangeStr($("#salaryperioditems-cost_of_children").val());
    let $advance_money = ChangeStr($("#salaryperioditems-advance_money").val());
    let $insurance_addition = ChangeStr($("#salaryperioditems-insurance_addition").val());
    let $cost_of_trust = ChangeStr($("#salaryperioditems-cost_of_trust").val());
    let $commission = ChangeStr($("#salaryperioditems-commission").val());
    let $non_cash_commission = ChangeStr($("#salaryperioditems-non_cash_commission").val());
    let $basic_salary_hours = parseFloat($basic_salary / 7.33);

    let $cost_of_food = 0;
    let $cost_of_house = 0;
    let $cost_of_spouse = 0;
    let $rate_of_year = 0;
    if ($hours_of_work == $countDay) {
        $cost_of_food = parseInt($("#salaryperioditems-cost_of_food").data('full'));
        $cost_of_house = parseInt($("#salaryperioditems-cost_of_house").data('full'));
        $cost_of_spouse = parseInt($("#salaryperioditems-cost_of_spouse").data('full'));
        $rate_of_year = parseInt($("#salaryperioditems-rate_of_year").data('full'));
    } else {
        $cost_of_food = Math.round($("#salaryperioditems-cost_of_food").data('value') * $hours_of_work);
        $cost_of_house = Math.round($("#salaryperioditems-cost_of_house").data('value') * $hours_of_work);
        $cost_of_spouse = Math.round($("#salaryperioditems-cost_of_spouse").data('value') * $hours_of_work);
        $rate_of_year = Math.round($("#salaryperioditems-rate_of_year").data('value') * $hours_of_work);
    }

    $("label[for='salaryperioditems-cost_of_food'] span").html(currencyFormat($cost_of_food.toString()));
    $("label[for='salaryperioditems-cost_of_house'] span").html(currencyFormat($cost_of_house.toString()));
    $("label[for='salaryperioditems-cost_of_spouse'] span").html(currencyFormat($cost_of_spouse.toString()));
    $("label[for='salaryperioditems-rate_of_year'] span").html(currencyFormat($rate_of_year.toString()));

    $("#salaryperioditems-cost_of_food").val($cost_of_food);
    $("#salaryperioditems-cost_of_house").val($cost_of_house);
    $("#salaryperioditems-cost_of_spouse").val($cost_of_spouse);
    $("#salaryperioditems-rate_of_year").val($rate_of_year);

    let $total_salary = ($hours_of_work * $basic_salary);
    let $formulaSalary = "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "کارکرد" + " (" + $hours_of_work + ")" + "*" + "دستمزد" + " (" + currencyFormat($basic_salary.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";

    if ($("#salaryperioditems-cost_of_food").is(':checked')) {
        $total_salary += $cost_of_food;
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "حق بن و خوارو بار" + " (" + currencyFormat($cost_of_food.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }
    if ($("#salaryperioditems-cost_of_house").is(':checked')) {
        $total_salary += $cost_of_house;
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "حق مسکن" + " (" + currencyFormat($cost_of_house.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }
    if ($("#salaryperioditems-rate_of_year").is(':checked')) {
        $total_salary += $rate_of_year;
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "حق عائله مندی" + " (" + currencyFormat($rate_of_year.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }
    if ($("#salaryperioditems-cost_of_spouse").is(':checked')) {
        $total_salary += $cost_of_spouse;
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "سنوات" + " (" + currencyFormat($cost_of_spouse.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }
    $total_salary += $commission;
    if ($commission > 0) {
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "پاداش" + " (" + currencyFormat($commission.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }

    $total_salary += $cost_of_trust;
    if ($cost_of_trust > 0) {
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "حق مسئولیت" + " (" + currencyFormat($cost_of_trust.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }

    if ($("#salaryperioditems-hours_of_overtime").val() > 0) {
        let $hours_of_overtime = parseInt(parseFloat($("#salaryperioditems-hours_of_overtime").val()) * parseFloat($("#salaryperioditems-hours_of_overtime").data('value')) * $basic_salary_hours);
        $total_salary += $hours_of_overtime;
        $("#salaryperioditems-hours_of_overtime_cost").val(currencyFormat($hours_of_overtime.toString()));
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "اضافه کاری" + " (" + currencyFormat($hours_of_overtime.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    } else {
        $("#salaryperioditems-hours_of_overtime_cost").val('');
    }
    if ($("#salaryperioditems-count_point").val() > 0) {
        let $cost_point = parseInt(parseFloat($("#salaryperioditems-count_point").val()) * parseFloat($("#salaryperioditems-count_point").data('value')));
        $total_salary += $cost_point;
        $("#salaryperioditems-cost_point").val(currencyFormat($cost_point.toString()));
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "امتیاز" + " (" + currencyFormat($cost_point.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    } else {
        $("#salaryperioditems-cost_point").val('');
    }
    if ($("#salaryperioditems-holiday_of_overtime").val() > 0) {
        let $holiday_of_overtime = parseInt(parseFloat($("#salaryperioditems-holiday_of_overtime").val()) * parseFloat($("#salaryperioditems-holiday_of_overtime").data('value')) * $basic_salary_hours);
        $total_salary += $holiday_of_overtime;
        $("#salaryperioditems-holiday_of_overtime_cost").val(currencyFormat($holiday_of_overtime.toString()));
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "تعطیل کاری" + " (" + currencyFormat($holiday_of_overtime.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    } else {
        $("#salaryperioditems-holiday_of_overtime_cost").val('');
    }
    if ($("#salaryperioditems-night_of_overtime").val() > 0) {
        let $night_of_overtime = parseInt(parseFloat($("#salaryperioditems-night_of_overtime").val()) * parseFloat($("#salaryperioditems-night_of_overtime").data('value')) * $basic_salary_hours);
        $total_salary += $night_of_overtime;
        $("#salaryperioditems-night_of_overtime_cost").val(currencyFormat($night_of_overtime.toString()));
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "شب کاری" + " (" + currencyFormat($night_of_overtime.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    } else {
        $("#salaryperioditems-night_of_overtime_cost").val('');
    }
    if ($("#salaryperioditems-hoursoflowtime").val() > 0) {
        let $hours_of_low_time = parseInt(parseFloat($("#salaryperioditems-hoursoflowtime").val()) * parseFloat($("#salaryperioditems-hoursoflowtime").data('value')) * $basic_salary_hours);
        $total_salary -= $hours_of_low_time;
        $("#salaryperioditems-hoursoflowtimecost").val(currencyFormat($hours_of_low_time.toString()));
        $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "کسر کار" + " (" + currencyFormat($hours_of_low_time.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    } else {
        $("#salaryperioditems-hoursoflowtimecost").val('');
    }

    let $insurance = 0;
    let $insurance_owner = 0;
    let $formulaInsurance = $formulaSalary;
    if ($isManager) {
        let $amount = ($hours_of_work * $basic_salary);
        $insurance = parseInt($amount * $("#salaryperioditems-insurance").data('value')); // بیمه
        $insurance_owner = parseInt($amount * $("#salaryperioditems-insurance_owner").data('value')); // بیمه کارفرما

        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ مشمول بیمه" + " = " + currencyFormat($amount.toString()) + " - عضو هیات مدیره " + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "نرخ بیمه کارمند" + " = " + $("#salaryperioditems-insurance").data('value') + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "نرخ بیمه کارفرما" + " = " + $("#salaryperioditems-insurance_owner").data('value') + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ بیمه کارمند" + " = " + currencyFormat($insurance.toString()) + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ بیمه کارفرما" + " = " + currencyFormat($insurance_owner.toString()) + "</p>";
    } else {
        $insurance = parseInt($total_salary * $("#salaryperioditems-insurance").data('value')); // بیمه
        $insurance_owner = parseInt($total_salary * $("#salaryperioditems-insurance_owner").data('value')); // بیمه کارفرما
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ مشمول بیمه" + " = " + currencyFormat($total_salary.toString()) + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "نرخ بیمه کارمند" + " = " + $("#salaryperioditems-insurance").data('value') + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "نرخ بیمه کارفرما" + " = " + $("#salaryperioditems-insurance_owner").data('value') + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ بیمه کارمند" + " = " + currencyFormat($insurance.toString()) + "</p>";
        $formulaInsurance += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ بیمه کارفرما" + " = " + currencyFormat($insurance_owner.toString()) + "</p>";
    }

    let $immunity_insurance = $("#salaryperioditems-insurance").data('immunity_insurance');

    $total_salary += $cost_of_children; // حق اولاد مالیات دارد بیمه ندارد
    if ($cost_of_children > 0) {
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "حق اولاد" + " (" + currencyFormat($cost_of_children.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";

    }
    // مزایای غیر نقدی مالیات ندارد بیمه دارد
    let $formulaTax = $formulaSalary;
    let $exempt = parseInt($insurance * $immunity_insurance);
    let $tax = calculateTax($total_salary - $exempt, $hours_of_work, $countDay, $formulaTax, $exempt);

    $total_salary += $non_cash_commission; // مزایای غیر نقدی مالیات ندارد بیمه ندارد
    if ($non_cash_commission > 0) {
        $formulaSalary += "<p>" + "<i class='fa fa-plus pull-right mr-2'></i>" + "مزایای غیر نقدی" + " (" + currencyFormat($non_cash_commission.toString()) + ")" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    }
    $formulaSalary += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "ناخالص" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "بیمه" + " (" + currencyFormat($insurance.toString()) + ")" + " = " + currencyFormat(($total_salary - $insurance).toString()) + "</p>";
    $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "مالیات" + " (" + currencyFormat($tax.toString()) + ")" + " = " + currencyFormat(($total_salary - $insurance - $tax).toString()) + "</p>";

    let $payment_salary = $total_salary - $insurance - $tax; // خالص حقوق

    $formulaSalary += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "خالص" + " = " + currencyFormat($payment_salary.toString()) + "</p>";

    $("#salaryperioditems-total_salary").val(currencyFormat($total_salary.toString()));

    $("#salaryperioditems-insurance").val(currencyFormat($insurance.toString()));

    $("#salaryperioditems-insurance_owner").val(currencyFormat($insurance_owner.toString()));

    $("#salaryperioditems-tax").val(currencyFormat($tax.toString()));


    $("#salaryperioditems-payment_salary").val(currencyFormat($payment_salary.toString()));

    let $final_payment = $payment_salary - $advance_money - $insurance_addition - $non_cash_commission; // پرداختی حقوق

    if($advance_money>0)
    {
        $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "مساعده" + " (" + currencyFormat($advance_money.toString()) + ")" + " = " + currencyFormat(($payment_salary - $advance_money).toString()) + "</p>";
    }
    if($insurance_addition>0)
    {
        $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "بیمه تکمیلی" + " (" + currencyFormat($insurance_addition.toString()) + ")" + " = " + currencyFormat(($payment_salary - $advance_money - $insurance_addition).toString()) + "</p>";
    }
    if($non_cash_commission>0)
    {
        $formulaSalary += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>" + "مزایای غیر نقدی" + " (" + currencyFormat($non_cash_commission.toString()) + ")" + " = " + currencyFormat(($payment_salary - $advance_money - $insurance_addition - $non_cash_commission).toString()) + "</p>";
    }


    $formulaSalary += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "پرداختی" + " = " + currencyFormat($final_payment.toString()) + "</p>";

    $("#salaryperioditems-final_payment").val(currencyFormat($final_payment.toString()));


    $("#formulaModalContent #formulaSalary").html($formulaSalary);
    $("#formulaModalContent #formulaInsurance").html($formulaInsurance);
}

function calculateCostOfChildren() {
    let $cost_of_children = $("#salaryperioditems-count_of_children").find(':selected').data('value');
    let $hours_of_work = parseInt($("#salaryperioditems-hours_of_work").val()) + parseInt($("#salaryperioditems-treatment_day").val());
    let $countDay = parseInt($("#salaryperioditems-hours_of_work").data('count-day'));
    if ($hours_of_work < $countDay) {
        $cost_of_children = parseInt($("#salaryperioditems-count_of_children").find(':selected').data('value') / $countDay * $hours_of_work);
    }

    $("#salaryperioditems-cost_of_children").val(currencyFormat($cost_of_children.toString()));
}

function calculateCostOfChildrenAndSalary() {
    calculateCostOfChildren();
    calculateSalary();
}

function calculateTax($total_salary, $hours_of_work, $countDay, $formulaTax, $exempt) {
    $formulaTax += "<p>" + "<i class='fa fa-minus pull-right mr-2'></i>معافیت بیمه" + "" + " = " + currencyFormat($exempt.toString()) + "</p>";
    $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ مشمول مالیات" + " = " + currencyFormat($total_salary.toString()) + "</p>";
    let $tax = 0;
    let $taxStep = 0;
    let $cost_tax_step_1 = $("#salaryperioditems-tax").data('step-1-min') * $hours_of_work / $countDay;
    let $cost_tax_step_2 = $("#salaryperioditems-tax").data('step-2-min') * $hours_of_work / $countDay;
    let $cost_tax_step_3 = $("#salaryperioditems-tax").data('step-3-min') * $hours_of_work / $countDay;
    let $cost_tax_step_4 = $("#salaryperioditems-tax").data('step-4-min') * $hours_of_work / $countDay;

    if ($total_salary > $cost_tax_step_1) {
        let $step_1_salary = Math.min(($total_salary - $cost_tax_step_1), ($cost_tax_step_2 - $cost_tax_step_1));
        $taxStep = parseInt($step_1_salary * $("#salaryperioditems-tax").data('step-1-percent'));
        $tax += $taxStep;
        $formulaTax += "<p class='text-center'>---------------- بازه اول بیشتر از " + currencyFormat($cost_tax_step_1.toString()) + " ----------------</p>";
        $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "ضریب" + " (" + $("#salaryperioditems-tax").data('step-1-percent') + ")" + "*" + "مبلغ" + " (" + currencyFormat($step_1_salary.toString()) + ")" + " = " + currencyFormat($taxStep.toString()) + "</p>";
    }

    if ($total_salary > $cost_tax_step_2) {
        let $step_2_salary = Math.min(($total_salary - $cost_tax_step_2), ($cost_tax_step_3 - $cost_tax_step_2));
        $taxStep = parseInt($step_2_salary * $("#salaryperioditems-tax").data('step-2-percent'));
        $tax += $taxStep;
        $formulaTax += "<p class='text-center'>---------------- بازه دوم بیشتر از " + currencyFormat($cost_tax_step_2.toString()) + " ----------------</p>";
        $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "ضریب" + " (" + $("#salaryperioditems-tax").data('step-2-percent') + ")" + "*" + "مبلغ" + " (" + currencyFormat($step_2_salary.toString()) + ")" + " = " + currencyFormat($taxStep.toString()) + "</p>";
    }

    if ($total_salary > $cost_tax_step_3) {
        let $step_3_salary = Math.min(($total_salary - $cost_tax_step_3), ($cost_tax_step_4 - $cost_tax_step_3));
        $taxStep = parseInt($step_3_salary * $("#salaryperioditems-tax").data('step-3-percent'));
        $tax += $taxStep;
        $formulaTax += "<p class='text-center'>---------------- بازه سوم بیشتر از " + currencyFormat($cost_tax_step_3.toString()) + " ----------------</p>";
        $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "ضریب" + " (" + $("#salaryperioditems-tax").data('step-3-percent') + ")" + "*" + "مبلغ" + " (" + currencyFormat($step_3_salary.toString()) + ")" + " = " + currencyFormat($taxStep.toString()) + "</p>";
    }

    if ($total_salary > $cost_tax_step_4) {
        let $step_4_salary = $total_salary - $cost_tax_step_4;
        $taxStep = parseInt($step_4_salary * $("#salaryperioditems-tax").data('step-4-percent'));
        $tax += $taxStep;

        $formulaTax += "<p class='text-center'>---------------- بازه چهارم بیشتر از " + currencyFormat($cost_tax_step_4.toString()) + " ----------------</p>";
        $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "ضریب" + " (" + $("#salaryperioditems-tax").data('step-4-percent') + ")" + "*" + "مبلغ" + " (" + currencyFormat($step_4_salary.toString()) + ")" + " = " + currencyFormat($taxStep.toString()) + "</p>";
    }

    $formulaTax += "<p class='text-center'>--------------------------------</p>";
    $formulaTax += "<p>" + "<i class='fa fa-check pull-right mr-2'></i>" + "مبلغ مالیات" + " = " + currencyFormat($tax.toString()) + "</p>";
    $("#formulaModalContent #formulaTax").html($formulaTax);
    return $tax;
}

function updateNextSalaryPeriodItems(result, options = {}) {
    var $nextBtn = $("<a>");
    $nextBtn.attr("url", "javascript:void(0)").attr("id", "123456789").attr("data-size", "modal-xl").attr("data-toggle", "modal").attr("data-target", "#modal-pjax").attr("data-url", result.ModalUrl).attr("data-title", result.ModalTitle).attr("data-reload-pjax-container", "p-jax-salary-period-items");
    $("body").append($nextBtn);
    setTimeout(() => {
        $nextBtn[0].click();
        $nextBtn.remove();
    }, 500);
}