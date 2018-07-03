<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Report;
use App\User;
use App\Category;
use Validator;
use Intervention\Image\ImageManagerStatic as Image;

class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::all();


        foreach($reports as $report)
        {
           $r_cat = Category::find($report->category_id)->get();

           $report->category_id = $r_cat[0]->category;
        }


       // $reports['category_id'] = $r_cat;

        return $this->sendResponse($reports->toArray(), 'Reports retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();


        $validator = Validator::make($input, [

            'files.*'=>'required|image|mimes:jpeg,png,jpg|max:3000',
            'description' => 'required',
            'category_id' => 'required',
        ],[
            'files.*.required' => 'Please upload an image',
            'files.*.mimes' => 'Only jpeg,png and bmp images are allowed',
            'files.*.max' => 'Sorry! Maximum allowed size for an image is 3MB',
        ]);



        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $image_path = null;

        foreach($request->file('files') as $file)
        {
            $img = Image::make($file);
            $unix_timestamp = strtotime(date("Y-m-d h:i:sa"));
            $fileName = $unix_timestamp.'-'.uniqid().'-'.\Auth::user()->id.'.'. $file->getClientOriginalExtension();
            $filePath = storage_path('/uploads/'.$fileName);
            $img->save($filePath);
            $image_path =$fileName  . ';' . $image_path;
        }

        $imgThumb = $img->resize(null, 75, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

        $fileNameThumb = 'thumb-'.$fileName;
        $filePathThumb = 'thumbnails/'.$fileNameThumb;
        $imgThumb->save($filePathThumb);

        $input['image_path']  = $image_path;
        $input['reported_by'] = \Auth::user()->id;
        $input['thumbnail']   = asset($filePathThumb);
        $reports = Report::create($input);


        return $this->sendResponse($reports->toArray(), 'Report created successfully.');
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByStatus($id)
    {
        $reports = Report::where('status',$id)->get();


        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }


        return $this->sendResponse($reports->toArray(), 'Report retrieved successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByUser($id)
    {
        $reports = Report::where('reported_by',$id)->get();


        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }


        return $this->sendResponse($reports->toArray(), 'Report retrieved successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id,$user_id
     * @return \Illuminate\Http\Response
     */
    public function showByUserAndStatus($user_id,$id)
    {
        $reports = Report::where('reported_by',$user_id)
            ->where('status',$id)
            ->get();


        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }


        return $this->sendResponse($reports->toArray(), 'Report retrieved successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reports = Report::find($id);


        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }


        return $this->sendResponse($reports->toArray(), 'Report retrieved successfully.');
    }


    /**
     * Display the count of report,pending and completed by user.
     *
     * route name /report/main
     * @return \Illuminate\Http\Response
     */
    public function showMain()
    {

        $reports = User::select('id',
                                'name',
                                'email',
                                'role',
                                'status',
                                'position',
                                'deviceType',
                                'deviceID',
                                'firebaseToken',
                                \DB::raw('(SELECT COUNT(id) from esco_maintenance_app.reports where reported_by=users.id) as `Report Count`'),
                                \DB::raw('(SELECT COUNT(id) from esco_maintenance_app.reports where reported_by=users.id and status = 1) as `Completed`'),
                                \DB::raw('(SELECT COUNT(id) from esco_maintenance_app.reports where reported_by=users.id and status = 0) as `Pending`'))->get();





        /*$reports = Report::select(\DB::raw('esco_maintenance_app.reports.id,
                            esco_maintenance_app.reports.image_path,
                            esco_maintenance_app.reports.status,
                            esco_maintenance_app.reports.category_id,
                            esco_maintenance_app.report_categories.category,
                            esco_maintenance_app.users.id as user_id,
                            esco_maintenance_app.users.name,
                            esco_maintenance_app.reports.thumbnail,
                            count(esco_maintenance_app.reports.id) as report_count,
                            count(if(esco_maintenance_app.reports.status=0,1,null)) as pending_count,
                            count(if(esco_maintenance_app.reports.status=1,1,null)) as completed_count,
                            esco_maintenance_app.reports.created_at'))
            ->join('users','users.id','=','reports.reported_by')
            ->join('report_categories','report_categories.id','=','reports.category_id')
            ->groupBy('reported_by')
            ->get();*/

        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }



        /*$index=0;
        foreach($reports as $report)
        {
           $r_by = User::where('id',$report->reported_by)->select('id as user_id','name')->get();

           $r = ['user_id'=>$r_by[$index]->id,'name'=>$r_by[$index]->name];

           array_push($report->toArray(),$r);

           $index++;
        }
*/

        return $this->sendResponse($reports, 'Report retrieved successfully.');
    }


    /**
     * Display the resource by users.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function showGroupByUser()
    {
        $reports = Report::select('esco_maintenance_app.reports.id',
                            'esco_maintenance_app.reports.image_path',
                            'esco_maintenance_app.reports.status',
                            'esco_maintenance_app.reports.thumbnail',
                            'esco_maintenance_app.reports.created_at',
                            'esco_maintenance_app.reports.category_id',
                            'esco_maintenance_app.report_categories.category',
                            'esco_maintenance_app.users.id as user_id',
                            'esco_maintenance_app.users.name')
            ->join('users','users.id','=','reports.reported_by')
            ->join('report_categories','report_categories.id','=','reports.category_id')
            ->get();


        if (is_null($reports)) {
            return $this->sendError('No record found!');
        }

        $arr = [];

        foreach($reports->toArray() as $report){
            $arr[$report['name']][] =
                 [   'report_id'  => $report['id'],
                     'image_path' => $report['image_path'],
                     'status'     => $report['status'],
                     'thumbnail'  => $report['thumbnail'],
                     'created_at' => $report['created_at'],
                     'category_id'=> $report['category_id'],
                     'category'   => $report['category'],
                     'user_id'    => $report['user_id'],
                     'name'       => $report['name']
                 ];
        }

        return $this->sendResponse($arr, 'Report retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $report->name = $input['name'];
        $report->detail = $input['detail'];
        $report->save();


        return $this->sendResponse($report->toArray(), 'Report successfully updated.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        $report->delete();


        return $this->sendResponse($report->toArray(), 'Report successfully deleted.');
    }



    public function ChangeStatus($id)
    {


        if(\Auth::check() && \Auth::user()->isAdmin() && \Auth::user()->isActivated()){

            $report = Report::find($id);

            if (is_null($report)) {
                return $this->sendError('No record found!');
            }

            $report->status = 1;//$report->status == 0 ? 1 : 0;
            $report->save();

            return $this->sendResponse($report->toArray(), 'Report successfully updated.');

        }

        return $this->sendError('Unauthorized!');

    }

}
