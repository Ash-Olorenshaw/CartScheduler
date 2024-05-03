<?php

namespace App\Http\Requests;

use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;

class UserAvailabilityRequest extends FormRequest
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @noinspection PhpUnused
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        // TODO AFTER upgrade to laravel V10, THIS NEEDS TO BE CONVERTED TO THE FUNCTION after()
        $settings = app()->make(GeneralSettings::class);
        $validator->after(function (Validator $validator) use ($settings) {
            if (!$settings->enableUserAvailability) {
                $validator->errors()->add('featureDisabled', 'Choosing availability is not enabled.');
            }
        });
        // Laravel V10 syntax
        //        return [
        //            function (Validator $validator) use ($settings) {
        //                if (!$settings->enableUserLocationChoices) {
        //                    $validator->errors()->add('featureDisabled', 'User location choices are not enabled.');
        //                }
        //            },
        //        ];
    }


    public function rules(): array
    {
        // These rules can produce unexpected results due to the prepareForValidation method manipulating the data
        return [
            'user_id' => ['integer', 'exists:users,id'],

            'day_monday'      => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_mondays', 0) > 0, ['array', 'min:2'])],
            'day_monday.*'    => ['required', 'integer', 'min:0', 'max:23'],
            'day_tuesday'     => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_tuesdays', 0) > 0, ['array', 'min:2'])],
            'day_tuesday.*'   => ['required', 'integer', 'min:0', 'max:23'],
            'day_wednesday'   => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_wednesdays', 0) > 0, ['array', 'min:2'])],
            'day_wednesday.*' => ['required', 'integer', 'min:0', 'max:23'],
            'day_thursday'    => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_thursdays', 0) > 0, ['array', 'min:2'])],
            'day_thursday.*'  => ['required', 'integer', 'min:0', 'max:23'],
            'day_friday'      => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_fridays', 0) > 0, ['array', 'min:2'])],
            'day_friday.*'    => ['required', 'integer', 'min:0', 'max:23'],
            'day_saturday'    => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_saturdays', 0) > 0, ['array', 'min:2'])],
            'day_saturday.*'  => ['required', 'integer', 'min:0', 'max:23'],
            'day_sunday'      => ['nullable', 'present', Rule::when(static fn(Fluent $data) => $data->get('num_sundays', 0) > 0, ['array', 'min:2'])],
            'day_sunday.*'    => ['required', 'integer', 'min:0', 'max:23'],

            'num_mondays'    => ['required', 'integer', 'min:0', 'max:4'],
            'num_tuesdays'   => ['required', 'integer', 'min:0', 'max:4'],
            'num_wednesdays' => ['required', 'integer', 'min:0', 'max:4'],
            'num_thursdays'  => ['required', 'integer', 'min:0', 'max:4'],
            'num_fridays'    => ['required', 'integer', 'min:0', 'max:4'],
            'num_saturdays'  => ['required', 'integer', 'min:0', 'max:4'],
            'num_sundays'    => ['required', 'integer', 'min:0', 'max:4'],

            'comments' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->parseDay('monday');
        $this->parseDay('tuesday');
        $this->parseDay('wednesday');
        $this->parseDay('thursday');
        $this->parseDay('friday');
        $this->parseDay('saturday');
        $this->parseDay('sunday');
    }

    protected function parseDay(string $dayOfWeek): void
    {
        if (empty($this->input('day_' . $dayOfWeek)) || $this->input("num_{$dayOfWeek}s", 0) === 0) {
            // if there are no 'days' set for $dayOfWeek, reset the day to an empty array
            $this->merge(["day_$dayOfWeek" => null]);
            return;
        }

        // if there are 'days' set for $dayOfWeek, make sure the full range of hours are set
        $hours = $this->input("day_$dayOfWeek", []);
        $first = $hours[0] ?? 0;
        $last  = $hours[count($hours) - 1] ?? 0;
        $this->merge(["day_$dayOfWeek" => range($first, $last)]);
    }

    public function authorize(): bool
    {
        return true;
    }
}
