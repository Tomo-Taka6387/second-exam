<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'new_clock_in'  => ['required'],
            'new_clock_out' => ['required'],

            'new_break_in'  => ['nullable'],
            'new_break_out' => ['nullable'],

            'new_break2_in'  => ['nullable'],
            'new_break2_out' => ['nullable'],

            'comment' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'new_clock_in.required'  => '出勤時間を入力してください',
            'new_clock_out.required' => '退勤時間を入力してください',
            'comment.required'       => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            $clock_in  = $data['new_clock_in'] ?? null;
            $clock_out = $data['new_clock_out'] ?? null;
            $break_in  = $data['new_break_in'] ?? null;
            $break_out = $data['new_break_out'] ?? null;
            $break2_in  = $data['new_break2_in'] ?? null;
            $break2_out = $data['new_break2_out'] ?? null;

            if ($clock_in && $clock_out && strtotime($clock_out) <= strtotime($clock_in)) {
                $validator->errors()->add('new_clock_out', '出勤時間もしくは退勤時間が不適切な値です');
            }

            if ($break_in) {
                if (($clock_in && strtotime($break_in) < strtotime($clock_in)) ||
                    ($clock_out && strtotime($break_in) > strtotime($clock_out))
                ) {
                    $validator->errors()->add('new_break_in', '休憩開始時間が不適切な値です');
                    return;
                }
            }

            if ($break_out) {
                if (($break_in && strtotime($break_out) < strtotime($break_in)) ||
                    ($clock_out && strtotime($break_out) > strtotime($clock_out))
                ) {
                    $validator->errors()->add('new_break_out', '休憩終了時間が不適切な値です');
                }
            }



            if ($break2_in) {
                if (($clock_in && strtotime($break2_in) < strtotime($clock_in)) ||
                    ($clock_out && strtotime($break2_in) > strtotime($clock_out))
                ) {
                    $validator->errors()->add('new_break2_in', '休憩時間が不適切な値です');
                }
            }

            if ($break2_out) {
                if (($break2_in && strtotime($break2_out) < strtotime($break2_in)) ||
                    ($clock_out && strtotime($break2_out) > strtotime($clock_out))
                ) {
                    $validator->errors()->add('new_break2_out', '休憩時間もしくは退勤時間が不適切な値です');
                }
            }


            if (empty($data['comment'])) {
                $validator->errors()->add('comment', '備考を記入してください');
            }
        });
    }
}
