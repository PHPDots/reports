<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Village;

class Member extends Model
{
    public $timestamps = true;
    protected $table = TBL_MEMBER;
    
    protected $fillable = ['firstname','lastname','mobile','village_id','address','professional','image','name','otp_counter','form_number','middlename','building','locality','organization','family_member_count','industry','blood_group_id','otp_status','otp_number','status','group_leader'];
    

   public static function getBloodGroups(){
   		$result = \DB::table(TBL_BLOOD_GROUP)
                ->pluck('title', 'id')
                ->all();
        return $result;
   }
   public static function getVillages(){
   		$result = \DB::table(TBL_VILLAGE)
                ->pluck('title', 'id')
                ->all();
        return $result;
   }
   public static function listFilter($query)
    {
        $search_start_date = request()->get("search_start_date");     
        $search_end_date = request()->get("search_end_date");  
        $search_id = request()->get("search_id");
        $search_firstname = request()->get("search_firstname");
        $search_middlename = request()->get("search_middlename");
		$search_lastname = request()->get("search_lastname");
        $search_mobile = request()->get("search_mobile");  
        $search_village = request()->get("search_village");      
        $search_professional = request()->get("search_professional");
	   	$search_status = request()->get("search_status");
        $is_download = request()->get("isDownload");

            if (!empty($search_start_date)){

                $from_date=$search_start_date.' 00:00:00';
                $convertFromDate= $from_date;

                $query = $query->where(TBL_MEMBER.".created_at",">=",addslashes($convertFromDate));
            }
            if (!empty($search_end_date)){

                $to_date=$search_end_date.' 23:59:59';
                $convertToDate= $to_date;

                $query = $query->where(TBL_MEMBER.".created_at","<=",addslashes($convertToDate));
            }
            if(!empty($search_id))
            {
                $idArr = explode(',', $search_id);
                $idArr = array_filter($idArr);
                if(count($idArr)>0)
                {
                    $query = $query->whereIn(TBL_MEMBER.".id",$idArr);
                } 
            }
            if(!empty($search_firstname))
            {
               $query = $query->where(TBL_MEMBER.".firstname", 'LIKE', '%'.$search_firstname.'%');
            }
            if(!empty($search_middlename))
            {
                $query = $query->where(TBL_MEMBER.".middlename", 'LIKE', '%'.$search_middlename.'%');
            }
	   		if(!empty($search_lastname))
            {
                $query = $query->where(TBL_MEMBER.".lastname", 'LIKE', '%'.$search_lastname.'%');
            }
            if(!empty($search_mobile))
            {
                $query = $query->where(TBL_MEMBER.".mobile", 'LIKE', '%'.$search_mobile.'%');
            }
            if(!empty($search_village))
            {
                $query = $query->where(TBL_VILLAGE.".id", $search_village);
            }
            if(!empty($search_professional))
            {
                $query = $query->where(TBL_MEMBER.".profession", 'LIKE', '%'.$search_professional.'%');
            }
	   		if($search_status == '1' || $search_status == '0')
            {
                $query = $query->where(TBL_MEMBER.".status", $search_status);
            }
            if(!empty($is_download) && $is_download =1)
            {
                $query = $query->get();
            }
        return $query;
    } 	

    public static function getMembers(){
        $members = array();

        $doctor = \DB::table(TBL_MEMBER)
            ->select
                (
                    \DB::raw("CONCAT(bopal_members.firstname,' ',bopal_members.middlename,' ',bopal_members.lastname) as name,id")
                )
            ->where("bopal_members.firstname",'!=','')
            ->where("bopal_members.lastname",'!=','')
			->where("bopal_members.status",1)
            ->get();

        foreach($doctor as $doc_data)
        {
            $members[$doc_data->id] = $doc_data->name;
        }    
        return $members;

    }
    
    public static function getloanMembers(){
        $members = array();
       

        $doctor = \DB::table(TBL_LOAN_MASTER)
            ->select(TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname",TBL_MEMBER.".id")
            ->join(TBL_MEMBER, TBL_LOAN_MASTER.".member_id", "=", TBL_MEMBER.".id")
            ->where(TBL_LOAN_MASTER.".status", "=", '0')
            ->groupBy(TBL_LOAN_MASTER.".member_id")
            ->get();
            
        foreach($doctor as $doc_data)
        {
            $members[$doc_data->id] = $doc_data->firstname.' '.$doc_data->middlename.' '.$doc_data->lastname;
        }    
        return $members;

    }
    public static function getaddloanMembers(){
        $members = array();
       

        $doctor = \DB::table(TBL_LOAN_BACHAT)
            ->select(TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname",TBL_MEMBER.".id")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id")
            ->groupBy(TBL_LOAN_BACHAT.".member_id")
            ->get();
            
        foreach($doctor as $doc_data)
        {
            $members[$doc_data->id] = $doc_data->firstname.' '.$doc_data->middlename.' '.$doc_data->lastname;
        }    
        return $members;

    }
    public static function getaddledgerMembers(){
        $members = array();
        
         $doctor =\DB::table(TBL_LEDGER)
            ->select(TBL_LEDGER.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname") 
            ->join(TBL_BB_ACOUNT, TBL_LEDGER.".bb_account_id", "=", TBL_BB_ACOUNT.".id")
            ->join(TBL_LOAN_BACHAT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id")
            ->groupBy(TBL_LOAN_BACHAT.".member_id")
            ->get();                   
        foreach($doctor as $doc_data)
        {
            $members[$doc_data->id] = $doc_data->firstname.' '.$doc_data->middlename.' '.$doc_data->lastname;
        }    
        return $members;

    }
    
}
