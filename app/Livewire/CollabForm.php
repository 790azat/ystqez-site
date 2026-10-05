<?php

namespace App\Livewire;

use App\Models\CollabRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Сотрудничество')]
class CollabForm extends Component
{
    public string $name = '';

    public string $contact = '';

    public string $company = '';

    #[Url(as: 'type', except: 'ads')]
    public string $type = 'ads';

    public string $budget = '';

    public string $message = '';

    public string $website = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'contact' => ['required', 'string', 'min:3', 'max:190', function ($attr, $value, $fail) {
                $v = trim($value);
                $isEmail = filter_var($v, FILTER_VALIDATE_EMAIL);
                $isTg = preg_match('~^(@|https?://t\.me/)?[A-Za-z0-9_]{4,32}$~', $v);
                $isPhone = preg_match('~^\+?[\d\s\-()]{7,20}$~', $v);
                if (! $isEmail && ! $isTg && ! $isPhone) {
                    $fail(__('Укажите email, Telegram (@username) или телефон.'));
                }
            }],
            'company' => ['nullable', 'string', 'max:190'],
            'type' => ['required', Rule::in(array_keys(CollabRequest::TYPES))],
            'budget' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => __('имя'), 'contact' => __('контакт'), 'company' => __('компания'),
            'type' => __('тип'), 'budget' => __('бюджет'), 'message' => __('сообщение'),
        ];
    }

    public function updated($field): void
    {
        if ($field !== 'website' && $field !== 'type') {
            $this->validateOnly($field);
        }
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            $this->sent = true;

            return;
        }
        $data = $this->validate();

        $key = 'collab:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('message', __('Слишком много заявок. Попробуйте позже.'));

            return;
        }
        RateLimiter::hit($key, 600);

        CollabRequest::create($data + ['ip' => request()->ip(), 'status' => 'new']);
        $this->reset('name', 'contact', 'company', 'budget', 'message');
        $this->sent = true;
    }

    public function again(): void
    {
        $this->sent = false;
    }

    public function render()
    {
        return view('livewire.collab-form');
    }
}
