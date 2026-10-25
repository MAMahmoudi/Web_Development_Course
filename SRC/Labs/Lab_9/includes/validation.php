<?php
// Server-side validation of the forms (Lab 6).
//
//   $errors = validate($values, [
//       'name'  => ['required', 'min:3', 'max:100'],
//       'email' => ['required', 'email'],
//       'phone' => ['phone'],                 // optional: only checked when it is not empty
//   ]);
//
// $errors is ['field' => 'message', ...]: empty when everything is valid.
// Each field gets one message: the one of the first rule that fails.

function validate(array $values, array $rules): array
{
    $errors = [];
    foreach ($rules as $field => $field_rules) {
        $value = $values[$field] ?? '';
        foreach ($field_rules as $rule) {
            // 'int:1,10' -> name 'int', parameter '1,10'
            [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, '');
            $error = check_rule($name, $parameter, (string) $value);
            if ($error !== null) {
                $errors[$field] = $error;
                break;
            }
        }
    }
    return $errors;
}

/** Returns the error message of one rule, or null when the value respects it. */
function check_rule(string $name, string $parameter, string $value): ?string
{
    // an empty value is only refused by "required": the other rules apply to what was typed
    if ($value === '' && $name !== 'required') {
        return null;
    }

    return match ($name) {
        'required' => $value === '' ? 'هذا الحقل إجباري.' : null,
        'min' => mb_strlen($value) < (int) $parameter ? "أدخل {$parameter} أحرف على الأقل." : null,
        'max' => mb_strlen($value) > (int) $parameter ? "{$parameter} حرف على الأكثر." : null,
        'email' => filter_var($value, FILTER_VALIDATE_EMAIL) === false ? 'البريد الإلكتروني غير صحيح.' : null,
        'phone' => preg_match('/^0[5-7][0-9]{8}$/', $value) === 1 ? null : 'رقم الهاتف يجب أن يكون من الشكل 05XXXXXXXX.',
        'int' => check_int($value, $parameter),
        'in' => in_array($value, explode(',', $parameter), true) ? null : 'الرجاء اختيار قيمة من القائمة.',
        'date' => check_date($value, (int) $parameter),
        default => throw new InvalidArgumentException("Unknown validation rule: $name"),
    };
}

/** 'int:1,10': a whole number between 1 and 10 ('int:0,' has no maximum). */
function check_int(string $value, string $range): ?string
{
    [$min, $max] = array_pad(explode(',', $range), 2, '');
    $options = [];
    if ($min !== '') {
        $options['min_range'] = (int) $min;
    }
    if ($max !== '') {
        $options['max_range'] = (int) $max;
    }
    if (filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]) !== false) {
        return null;
    }
    return $max === '' ? "أدخل عدداً صحيحاً أكبر من أو يساوي {$min}." : "أدخل عدداً صحيحاً بين {$min} و {$max}.";
}

/** 'date:3': a real date in the format YYYY-MM-DD, at least 3 days from today. */
function check_date(string $value, int $min_days): ?string
{
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    // createFromFormat accepts "2025-02-31" (it becomes March 3rd): compare with the original text
    if ($date === false || $date->format('Y-m-d') !== $value) {
        return 'التاريخ غير صحيح.';
    }
    if ($date < new DateTime("today +{$min_days} days")) {
        return "يجب أن يكون التاريخ بعد {$min_days} أيام على الأقل.";
    }
    return null;
}
