<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationBirthday;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\OaTemplate;
use App\Models\ZaloOa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutomationController extends Controller
{
    //
    public function user(){
        $oa_id = ZaloOa::where('is_active', 1)->first()->id;
        $template = OaTemplate::where('oa_id', $oa_id)->get();
        $user = AutomationUser::first();

        return view('admin.automation.user', compact('template', 'user'));
    }

    public function userupdate(Request $request){
        //  dd($request->all());
        $user = AutomationUser::first();
        $data = [
            'name'  => $request->name,
            'status' => $request->input('status', 0),
            'template_id' => $request->template_id
        ];
        if($user){
            $user->update($data);
            return redirect()->back()->with('success', 'Update thành công');
        }else{
            AutomationUser::create($data);
            return redirect()->back()->with('success', 'Update thành công');
        }
        return redirect()->back()->with('error', 'Update thất bại');
    }



    public function birthday(){
        $oa_id = ZaloOa::where('is_active', 1)->first()->id;
        $template = OaTemplate::where('oa_id', $oa_id)->get();
        $birthday = AutomationBirthday::first();

        return view('admin.automation.birthday', compact('template', 'birthday'));
    }

    public function birthdayupdate(Request $request){
        //  dd($request->all());
        $birthday = AutomationBirthday::first();
        $data = [
            'name'  => $request->name,
            'status' => $request->input('status', 0),
            'start_time' =>$request->start_time,
            'template_id' => $request->template_id
        ];
        if($birthday){

            $birthday->update($data);

            return redirect()->back()->with('success', 'Update thành công');
        }else{

            AutomationBirthday::create($data);
            return redirect()->back()->with('success', 'Update thành công');
        }
        return redirect()->back()->with('error', 'Update thất bại');
    }


    public function reminder(){
        $oa_id = ZaloOa::where('is_active', 1)->first()->id;
        $template = OaTemplate::where('oa_id', $oa_id)->get();
        $reminder = AutomationReminder::first();
        return view('admin.automation.reminder', compact('template', 'reminder'));
    }

    public function reminderupdate(Request $request){
        //   dd($request->all());
        $reminder = AutomationReminder::first();
        $data = [
            'name'  => $request->name,
            'status' => $request->input('status', 0),
            'sent_time' =>$request->sent_time,
            'template_id' => $request->template_id,
            'numbertime' => $request->numbertime
         ];
        if($reminder){

            $reminder->update($data);

            return redirect()->back()->with('success', 'Update thành công');
        }else{

            AutomationReminder::create($data);
            return redirect()->back()->with('success', 'Update thành công');
        }
        return redirect()->back()->with('error', 'Update thất bại');
    }

}
