<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'   => 'يجب قبول  :attribute.',
    'active_url' => ' :attribute ليس عنوان URL صالحًا.',
    'after'      => ' :attribute يجب أن يكون تاريخ بعد :date.',
    'alpha'      => ' :attribute قد تحتوي على أحرف فقط.',
    'alpha_dash' => ' :attribute قد تحتوي فقط على أحرف وأرقام وشرطات.',
    'alpha_num'  => ' :attribute قد تحتوي على أحرف وأرقام فقط.',
    'array'      => ' :attribute يجب أن يكون مصفوفة.',
    'before'     => ' :attribute يجب أن يكون تاريخ قبل :date.',
    'between'    => [
        'numeric' => ' :attribute يجب ان يكون بين :min و :max.',
        'file'    => ' :attribute يجب ان يكون بين :min و :max كيلوبايت.',
        'string'  => ' :attribute يجب ان يكون بين :min و :max حروف.',
        'array'   => ' :attribute يجب ان يكون بين :min و :max items.',
    ],
    'boolean'        => ' :attribute يجب أن يكون الحقل نعم أو لا.',
    'confirmed'      => ' :attribute التأكيد غير متطابق.',
    'date'           => ' :attribute هذا ليس تاريخ صحيح.',
    'date_format'    => ' :attribute لا يتطابق مع الشكل :format.',
    'different'      => ' :attribute و :or يجب أن تكون مختلف.',
    'digits'         => ' :attribute يجب ان يكون :digits digits.',
    'digits_between' => ' :attribute يجب ان  يكون بين  :min و :max digits.',
    'distinct'       => ' :attribute الحقل يحتوي على قيمة مكررة.',
    'email'          => ' :attribute يجب أن يكون عنوان بريد إلكتروني صالح.',
    'exists'         => ' المختار :attribute غير متاح.',
    'filled'         => ' :attribute  مطلوب',
    'image'          => ' :attribute يجب ان يكون صورة.',
    'in'             => ' المختار :attribute غير متاح.',
    'in_array'       => ' :attribute الحقل غير موجود في :other.',
    'integer'        => ' :attribute يجب ان يكون رقما.',
    'ip'             => ' :attribute يجب أن يكون عنوان IP صالحًا.',
    'json'           => ' :attribute يجب أن تكون سلسلة JSON صالحة.',
    'max'            => [
        'numeric' => ' :attribute قد لا يكون أكبر من :max.',
        'file'    => ' :attribute قد لا يكون أكبر من :max كيلوبايت.',
        'string'  => ' :attribute قد لا يكون أكبر من :max حروف.',
        'array'   => ' :attribute قد لا يكون أكثر من :max items.',
    ],
    'lte'   => [
        'numeric' => ' :attribute يجب أن يكون أكبر من المبلغ.',
    ],
    'mimes' => ' :attribute يجب أن يكون ملفًا من النوع: :values.',
    'min'   => [
        'numeric' => ' :attribute لا بد أن يكون على الأقل :min.',
        'file'    => ' :attribute لا بد أن يكون على الأقل :min كيلوبايت.',
        'string'  => ' :attribute لا بد أن يكون على الأقل :min حروف.',
        'array'   => ' :attribute يجب أن يكون على الأقل :min items.',
    ],
    'not_in'               => ' المختار :attribute غير متاح.',
    'numeric'              => ' :attribute يجب ان يكون رقم.',
    'present'              => ' :attribute يجب أن يكون الحقل موجودًا.',
    'regex'                => ' :attribute التنسيق غير صالح.',
    'required'             => ' :attribute الحقل مطلوب.',
    'required_if'          => ' :attribute الحقل مطلوب عندما :other مساوي :value.',
    'required_unless'      => ' :attribute الحقل مطلوب ما لم يكن :other في  :values.',
    'required_with'        => ' :attribute الحقل مطلوب عندما :values موجود.',
    'required_with_all'    => ' :attribute الحقل مطلوب عندما :values موجود.',
    'required_without'     => ' :attribute الحقل مطلوب عندما :values غير موجود.',
    'required_without_all' => ' :attribute الحقل مطلوب عندما لا شيء من :values موجود.',
    'same'                 => ' :attribute و :other يجب أن يكون متطابق.',
    'size'                 => [
        'numeric' => ' :attribute يجب ان يكون :size.',
        'file'    => ' :attribute يجب ان يكون :size كيلوبايت.',
        'string'  => ' :attribute يجب ان يكون :size حروف.',
        'array'   => ' :attribute must contain :size items.',
    ],
    'string'   => ' :attribute يجب ان يكون نص.',
    'timezone' => ' :attribute يجب ان يكون منطقة صحيحة.',
    'unique'   => ' :attribute موجود من قبل.',
    'url'      => ' :attribute الصيغة غير صحيحة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
