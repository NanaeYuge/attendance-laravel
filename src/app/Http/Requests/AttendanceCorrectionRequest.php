<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'clock_in' => ['nullable','date_format:H:i'],
            'clock_out' => ['nullable','date_format:H:i'],
            'breaks' => ['array'],
            'breaks.*.in' => ['nullable','date_format:H:i'],
            'breaks.*.out'=> ['nullable','date_format:H:i'],
            'note' => ['required','string'],
        ];
    }

    public function messages(): array
    {
        return [
            // 指定文言（評価項目）
            'note.required' => '備考を記入してください',
            'clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format'=> '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.in.date_format'  => '休憩時間が不適切な値です',
            'breaks.*.out.date_format' => '休憩時間が不適切な値です',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $v) {
            $in = $this->input('clock_in');
            $out = $this->input('clock_out');
            $breaks = $this->input('breaks', []);

            if ($in && $out && $in > $out) {
                $v->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }
            foreach ($breaks as $i => $b) {
                $bi = $b['in'] ?? null;
                $bo = $b['out'] ?? null;

                if ($in && $bi && $bi < $in) {
                    $v->errors()->add("breaks.$i.in", '休憩時間が不適切な値です');
                }
                if ($out && $bi && $bi > $out) {
                    $v->errors()->add("breaks.$i.in", '休憩時間が不適切な値です');
                }
                if ($out && $bo && $bo > $out) {
                    $v->errors()->add("breaks.$i.out", '休憩時間もしくは退勤時間が不適切な値です');
                }
                if ($bi && $bo && $bi > $bo) {
                    $v->errors()->add("breaks.$i.in", '休憩時間が不適切な値です');
                }
            }
        });
    }
}
