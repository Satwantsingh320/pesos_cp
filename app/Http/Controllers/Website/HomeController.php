<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
class HomeController extends Controller
{
    public function index()
    {
        $latest = Product::active()
            //->latest('id')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        $featured = Product::active()
            ->where('is_featured', 1)
            ->latest('id')
            ->limit(12)
            ->get();

        $clearence = Product::active()
            ->where('is_clearance', 1)
            ->latest('id')
            ->limit(12)
            ->get();
        //display random products as bestsessling for now
        $bestSelling = Product::active()
            ->orderByDesc('total_Sold')
            ->limit(12)
            ->get();
        return view('website.index', compact('clearence', 'latest', 'featured', 'bestSelling'));
    }
    public function contactUs()
    {
        return view('website.contact-us');
    }
    public function createTicket(Request $request)
    {
        $rules = array(
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => ['nullable'],
            'subject' => 'required|min:3|max:200', // max 200 characters
            'message' => [
                'required',
                'string',
                'max:2000', // adjust limit as needed
                'not_regex:/<script\b[^>]*>(.*?)<\/script>/i', // block <script> tags
                'not_regex:/<\/?[a-z][\s\S]*>/i', // block all HTML tags (optional)
            ],
        );
        $messages = [
            'message.not_regex' => 'Script tags or HTML are not allowed in the message.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withInput($request->all())->withErrors($validator)->withFragment('contact-us');
        } else {
            $date = date('Y-m-d H:i:s'); //this returns the current date time
            $middle = strtotime($date);
            $un_id = 'T' . $middle;
            $message = $request->message;
            //send mail
            $array['view'] = 'emails.supportTicket';
            $array['subject'] = $request->subject;
            $array['email'] = $request->email;
            $array['name'] = $request->person_name;
            $array['message'] = $message;
            $email = config('mail.admin_email');
            try {
                Mail::to($email)->send(new SupportEmail($array));
            } catch (\Exception $e) {
            }

            $validator = 'Ticket no: ' . $un_id . ' created successfully. Admin will contact you as soon as possible!';
            Session::flash('success', $validator);
            return redirect()->route('website.contact-us');
        }

    }

}
