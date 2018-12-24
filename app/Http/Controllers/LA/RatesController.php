<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use Carbon\Carbon;

use App\Models\Rate;

class RatesController extends Controller
{
	public $show_action = true;
	public $view_col = 'DATE';
	public $listing_cols = ['id', 'DATE', 'USD', 'EUR', 'GBP', 'RUB', 'UAH'];
	
	public function __construct() {
		// Field Access of Listing Columns
		//if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			//$this->middleware(function ($request, $next) {
				//$this->listing_cols = ModuleFields::listingColumnAccessScan('Rates', $this->listing_cols);
				//return $next($request);
			//});
		//} else {
			//$this->listing_cols = ModuleFields::listingColumnAccessScan('Rates', $this->listing_cols);
		//}
	}
	
	/**
	 * Display a listing of the Rates.
	 *
	 * @return \Illuminate\Http\Response
	 */
	
	public function index()
	{
		$module = Module::get('Rates');
		$this->getRates();
		if(Module::hasAccess($module->id)) {
			return View('la.rates.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
	}

	public function getRates()
	{
		$dataIn = Rate::all()->last();
		$dateSt = Carbon::now();	
		$dateStart =  date('Y-m-d', strtotime($dateSt));
		
		if($dataIn == null)
		{
			$n = 14;
		}
		else
		{
			$n = 1;		
		}
		
		for ($i=0;$i<$n;$i++){
			date_sub($dateSt, date_interval_create_from_date_string('1 day'));
			$d = $dateSt;
			$d = date('Ymd', strtotime($d));
			
			$data = simplexml_load_file('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?date='.$d);			
			if ($data){
				if((string)$data[0]->currency->exchangedate)
				{
					$rates = array();
					$rates += ['exchangedate' => (string)$data[0]->currency->exchangedate];	
					foreach($data as $rate){			
						$rates += [
							(string)$rate->cc	=> 	(float)$rate->rate,
						];
					}
			
					$date = $rates['exchangedate'];		
					$date = date('Y-m-d', strtotime($date));
					
						$baseCurrency = 'USD';
						$dataIn = new Rate;
						$dataIn->DATE = $date;
						$dataIn->USD = round((float)$rates[$baseCurrency]/$rates['USD'],2);
						$dataIn->EUR = round((float)$rates[$baseCurrency]/$rates['EUR'],2);
						$dataIn->GBP = round((float)$rates[$baseCurrency]/$rates['GBP'],2);
						$dataIn->RUB = round((float)$rates[$baseCurrency]/$rates['RUB'],2);
						$dataIn->UAH = round((float)$rates['USD'],2);
						$dataIn->save();
			
				}
				else {
					$n++;
					if ($n > 5){break;}
				}
			}else{continue;}			
			
		}
		
	}

	/**
	 * Show the form for creating a new rate.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created rate in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Rates", "create")) {
		
			$rules = Module::validateRules("Rates", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			$insert_id = Module::insert("Rates", $request);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.rates.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified rate.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Rates", "view")) {
			
			$rate = Rate::find($id);
			if(isset($rate->id)) {
				$module = Module::get('Rates');
				$module->row = $rate;
				
				return view('la.rates.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('rate', $rate);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("rate"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified rate.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Rates", "edit")) {			
			$rate = Rate::find($id);
			if(isset($rate->id)) {	
				$module = Module::get('Rates');
				
				$module->row = $rate;
				
				return view('la.rates.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('rate', $rate);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("rate"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified rate in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Rates", "edit")) {
			
			$rules = Module::validateRules("Rates", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			$insert_id = Module::updateRow("Rates", $request, $id);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.rates.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified rate from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Rates", "delete")) {
			Rate::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.rates.index');
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}
	
	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax()
	{
		$values = DB::table('rates')->select($this->listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Rates');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/rates/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Rates", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/rates/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("Rates", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.rates.destroy', $data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}

	
}
