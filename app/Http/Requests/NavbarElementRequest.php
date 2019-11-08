<?php

namespace Azuriom\Http\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Azuriom\Models\NavbarElement;
use Azuriom\Models\Page;
use Azuriom\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavbarElementRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The checkboxes attributes.
     *
     * @var array
     */
    protected $checkboxes = [
        'new_tab',
    ];

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->mergeCheckboxes();

        $this->merge([
            'value' => $this->getLinkValue()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['string', Rule::in(NavbarElement::types())],
            'link' => ['required_if:type,link', 'nullable', 'string', 'max:100'],
            'value' => ['sometimes'],
            'new_tab' => ['filled', 'boolean']
        ];
    }

    /**
     * Get the link value to store.
     *
     * @return string
     */
    protected function getLinkValue()
    {
        switch ($this->input('type')) {
            case 'page':
                $page = Page::find($this->input('page'));
                return $page ? $page->slug : '';
            case 'post':
                $post = Post::find($this->input('post'));
                return $post ? $post->slug : '';
            case 'link':
                return $this->input('link');
            default:
                return '#';
        }
    }
}