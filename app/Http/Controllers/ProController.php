<?php

namespace App\Http\Controllers;

use App\Book;
use App\Feed;
use App\Location;
use App\Profession;

use App\User;
use App\Word;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProController extends Controller
{
    
    
    
    
    
    
public function  show($pro)
{
    $prof= Profession::where('name',$pro)->first();
    $id=$prof->id;
    $locate=Auth::user()->location_id;

    $user = User::where('profession_id',$id)
        ->where('role_id',0)
        ->where('isactive',1)
        ->where('location_id',$locate)
        ->orderBy('rating', 'desc')
        ->get();


 return view('user.show',compact('user','pro'));





}


    public function profile($id)

    {

          $us = User::find($id);
        $edit = DB::table('feeds')
            ->where('workerid', $id)
            ->where('user_id', Auth::user()->id)

            ->count();

        $booked = DB::table('books')
            ->where('worker_id', $id)
            ->where('user_id', Auth::user()->id)

            ->count();
        $r = DB::table('feeds')
            ->where('workerid', $id)
            ->count();

        $r1 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>=',0)
            ->where('rating','<=',1)

            ->count();
        $r2 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',1)
            ->where('rating','<=',2)

            ->count();
        $r3 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',2)
            ->where('rating','<=',3)

            ->count();
        $r4 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',3)
            ->where('rating','<=',4)

            ->count();
        $r5 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',4)
            ->where('rating','<=',5)

            ->count();

        $feed =Feed::where('workerid', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
        $feeding=Feed::where('workerid', $id)->get();




       $dt=Carbon::today();
        if(Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count())
            $tom=1;
        else
            $tom=2;
        if(Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count())
            $tom1=1;
        else
            $tom1=2;
        if($c=Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count()) {
            $tom2 = 1;
        }
        else
            $tom2=2;


        $tt=Carbon::now();
   
       return view('user.profile',compact('us','edit','tom','tom1','tom2','tt','feed','feeding','booked','r','r1','r2','r3','r4','r5'));




    }

    public function store(Request $request,$id )
    {

        $this->validate($request, [
            'feed' => 'required|max:1000'

        ]);
        $feed = new Feed;

        $feed->workerid =$id;
          $feed->feedback =$request['feed'];
          $feed->rating =$request['rating'];
          $feed->user_id =Auth::user()->id;

           $feed->save();

        $this->rater($id);
        return  redirect()->back();
    }


    public function edit(Request $request,$id )
    {

        $this->validate($request, [
            'feed' => 'required|max:1000'

        ]);
        $feed =Feed::where('workerid', $id)
            ->where('user_id', Auth::user()->id)
            ->first();


         

     $feed->feedback =$request['feed'];
      $feed->rating =$request['rating'];


      $feed->update();
        $this->rater($id);
       return  redirect()->back();

    }


    public function delete($id)
    {
        $feed =Feed::where('workerid', $id)
            ->where('user_id', Auth::user()->id)
            ->delete();

        $r = DB::table('feeds')
            ->where('workerid', $id)
            ->count();

        $r1 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>=',0)
            ->where('rating','<=',1)
            ->count();

        $r2 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',1)
            ->where('rating','<=',2)
            ->count();

        $r3 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',2)
            ->where('rating','<=',3)
            ->count();

        $r4 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',3)
            ->where('rating','<=',4)
            ->count();

        $r5 = DB::table('feeds')
            ->where('workerid', $id)
            ->where('rating','>',4)
            ->where('rating','<=',5)
            ->count();

        $rate=$r1*1+$r2*2+$r3*3+$r4*4+$r5*5;
        if($r==0)
            $rate=0;
        else
            $rate=$rate/$r;

        $u=User::find($id);

        $u->rating=$rate;
        $u->update();

        return redirect()->back();

    }

    public function pay($id,$date,Request $request)
    {

        if($date==4&& $request['select']=='')
            return 'fail';
       $sel=0;
        $tom=0;
        $tom1=0;
        $tom2=0;
        $dt=Carbon::today();
        $book = new Book();

        $book->worker_id =$id;




        if($date==1)
            $book->time = $dt->addDays(1);
        if($date==2)
            $book->time = $dt->addDays(2);
        if($date==3)
            $book->time = $dt->addDays(3);
        if($date==4)
            $book->time = $request['select'];

        $book->user_id =Auth::user()->id;


       $dt=Carbon::today();
       if(Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count())
          $tom=1;

       if(Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count())
           $tom1=1;

       if($c=Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$dt->addDay()->toDateString())->count())
           $tom2 = 1;
        if(Book::where('accept','<>','2')->where('worker_id', $id)->where('time',$request['select'])->count())
           $sel=1;


        if($tom==1&&$date==1)

            return redirect()->back();
            elseif ($tom1==1&&$date==2)
                return redirect()->back();
        elseif ($tom2==1&&$date==3)
            return redirect()->back();
       elseif($sel==1)
          return 'tt';

       else
        {

            $book->save();
            $status = " Successfull  ......Worker Booked";

            return redirect('/users/order')->with('status', $status);
        }

    }

    public function order()
    {
          $orders=Book::where('user_id',Auth::user()->id)
              ->orderBy('created_at','desc')
              ->get();


        return view('user.order',compact('orders'));
    }

   public function rater($id)
   {

       $words=Word::all();
       $count=Word::all()->count();
       $i=0;

       foreach($words as $wor )

       {
           $i++;
           $word[$i]=$wor->word;
           $w[$i]=$wor->weight;




       }
        $feedrate=0;

       $feed =Feed::where('workerid', $id)
           ->where('user_id', Auth::user()->id)
           ->first();
       $c=0;

       for($i=1;$i<=$count;$i++)
       {

           if (str_is('*' . $word[$i] . '*', $feed->feedback)) {

               $c++;

               $feedrate += $w[$i];
           }


       }

       $not=0;

       if (str_is( '*not*', $feed->feedback)) {
            $not=1;

       }

       if($not)
       {
           if($feedrate>0)
               $feedrate = $feedrate/$c-2;
               else
                   $feedrate = $feedrate/$c+5;

       }

        else
         if($c!=0)
            $feedrate = $feedrate/$c+3;




       $feed->rating = ($feed->rating +$feedrate)/2;

       $feed->update();



       $r = DB::table('feeds')
           ->where('workerid', $id)
           ->count();

       $r1 = DB::table('feeds')
           ->where('workerid', $id)
           ->where('rating','>=',0)
           ->where('rating','<=',1)
           ->count();

       $r2 = DB::table('feeds')
           ->where('workerid', $id)
           ->where('rating','>',1)
           ->where('rating','<=',2)
           ->count();

       $r3 = DB::table('feeds')
           ->where('workerid', $id)
           ->where('rating','>',2)
           ->where('rating','<=',3)
           ->count();

       $r4 = DB::table('feeds')
           ->where('workerid', $id)
           ->where('rating','>',3)
           ->where('rating','<=',4)
           ->count();

       $r5 = DB::table('feeds')
           ->where('workerid', $id)
           ->where('rating','>',4)
           ->where('rating','<=',5)
           ->count();

       $rate=$r1*1+$r2*2+$r3*3+$r4*4+$r5*5;
       if($r==0)
           $rate=0;
       else
           $rate=$rate/$r;

       $u=User::find($id);

       $u->rating=$rate;
       $u->update();







   }
    
    
    public function edit_profile()
 {

     $location=Location::all();
    $profession=Profession::all();
    $user=User::find(Auth::user()->id);






    return view('user.edit',compact('location','profession','user'));

 }


    public function edited_profile(Request $request)
    {



        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'number' => 'required'

        ]);
        $location=Location::all();
        $profession=Profession::all();
        $user=User::find(Auth::user()->id);
        $user->number=$request['number'];
        $user->address=$request['address'];

        $user->location_id= $request['location_id'];
        $user->name=$request['name'];
        $user->email=$request['email'];
        $user->update();




        return redirect('/');

    }
    
    public function finish($id)
    {
        $book=Book::find($id);
        $book->finish=1;
        $book->update();
        
        return redirect()->back();
        
            
        
    }

    public function date()

{
    $dt=Carbon::now();
$book=Book::find(22);
    $book->time=Carbon::today();
    $book->update();
    echo $dt->format('l d F Y H i s');


}

}
