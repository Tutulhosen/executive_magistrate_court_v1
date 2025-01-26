<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArchiveCasePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'caseNo' => 'required',
            'caseDate' => 'required|date',
            'applicantName' => 'required',
            'defaulterName' => 'required',
            'lawSection' => 'required',
            'upa_section' => 'required',
            'lastorderDate' => 'required|date',
            'caseJudgmentNote' => 'required',
            'attached_file' => 'required|file|mimes:pdf,jpg,png|max:10240'
        ];
    }

    public function messages()
    {
        return [
            'caseNo.required' => 'মামলা নম্বর দিতে হবে.',
            'caseDate.required' => 'মামলার তারিখ দিতে হবে.',
            'caseDate.date' => 'অবৈধ মামলা তারিখ বিন্যাস.',
            'applicantName.required' => 'আবেদনকারীর নাম দিতে হবে.',
            'defaulterName.required' => 'বিবাদীর নাম দিতে হবে.',
            'lawSection.required' => 'অভিযোগের ধরণ দিতে হবে.',
            'upa_section.required' => 'উপজেলা  দিতে হবে.',
            'lastorderDate.required' => 'শেষ আদেশের তারিখ দিতে হবে.',
            'lastorderDate.date' => 'অবৈধ শেষ আদেশের তারিখ বিন্যাস.',
            'caseJudgmentNote.required' => 'মামলার রায় দিতে হবে.',
            'attached_file.required' => 'আদেশ এর সংযুক্তি ফাইলটি প্রদান করুন.',
            'attached_file.file' => 'সঠিক ফাইল টাইপ নোটিশ পুনঃ আপলোড করুন.',
            'attached_file.mimes' => 'ফাইলটির ধরনটি সমর্থন হয় না. অনুগ্রহ করে পুনরায় চেষ্টা করুন.',
            'attached_file.max' => 'ফাইলটি অত্যন্ত বড়, সর্বাধিক :max কিলোবাইট অনুমোদিত.',
        ];
    }
}
