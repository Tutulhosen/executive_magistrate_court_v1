<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCausePostRequest extends FormRequest
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

            'causeTitle' => 'required',
            'applicantName' => 'required|string',
            'caseNo' => 'required|string|unique:custom_causelist',
            'applicantMobile' => 'required|size:11|regex:/(01)[0-9]{9}/',
            'defaulterName' => 'required|string',
            'lawSection' => 'required|string',
            'caseDate' => 'required|date',
            'dis_section' => 'required|string',
            'div_section' => 'required|string',
            'upa_section' => 'required|string',
            'next_date' => 'required|date',
            'lastorderDate' => 'required|date',
            
        ];
    }

    public function messages(): array
    {
        return [
            'causeTitle.required' => 'মামলার আদেশের শিরনাম দিতে হবে',
            'caseNo.required' => 'মামলা নম্বর দিতে হবে',
            'caseNo.string' => 'মামলা নম্বর অবশ্যই স্ট্রিং হতে হবে',
            'caseNo.unique' => 'এই মামলা নাম্বার ইতপূর্বে ব্যবহার হয়েছে ',
            'caseDate.required' => 'মামলা তারিখ দিতে হবে',
            'caseDate.date' => 'মামলা তারিখ অবশ্যই তারিখ হতে হবে',
            'lawSection.required' => 'আইন ধারা দিতে হবে',
            'lawSection.string' => 'আইন ধারা অবশ্যই স্ট্রিং হতে হবে',
            'div_section.required' => 'বিভাগ দিতে হবে',
            'div_section.string' => 'বিভাগ অবশ্যই স্ট্রিং হতে হবে',
            'dis_section.required' => 'জেলা দিতে হবে',
            'dis_section.string' => 'জেলা অবশ্যই স্ট্রিং হতে হবে',
            'upa_section.required' => 'উপজেলা দিতে হবে',
            'upa_section.string' => 'উপজেলা অবশ্যই স্ট্রিং হতে হবে',
            'applicantName.required' => 'বাদীর নাম দিতে হবে',
            'applicantName.string' => 'বাদীর নাম অবশ্যই স্ট্রিং হতে হবে',
            'applicantMobile.required' => 'বাদীর মোবাইল নাম্বার দিতে হবে',
            'applicantMobile.size' => 'বাদীর মোবাইল নাম্বার ১১ সংখার হতে হবে',
            'applicantMobile.regex' => 'সঠিক মোবাইল নাম্বার প্রদান করুন',
            'defaulterName.required' => 'বিবাদীর নাম দিতে হবে',
            'defaulterName.string' => 'বিবাদীর নাম অবশ্যই স্ট্রিং হতে হবে',
            'next_date.required' => 'পরবর্তী তারিখ দিতে হবে',
            'lastorderDate.required' => 'শেষ আদেশের তারিখ দিতে হবে',
            'lastorderDate.date' => 'শেষ আদেশের তারিখ অবশ্যই তারিখ হতে হবে',
            'defaulter_name.required' => 'খাতকের নাম দিতে হবে ',
        ];
    }
}
