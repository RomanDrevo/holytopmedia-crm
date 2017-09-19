<?php

namespace App\Liantech\Helpers;


use Carbon\Carbon;

/**
* Generate data for the plan charts
*/
class ChartDataGenerator
{
	
	/**
	 * Cusotomer selected risk level
	 * 
	 * @var sting
	 */
	protected $customerData;

	/**
	 * Difference in days between the start and the end
	 * 
	 * @var int
	 */
	protected $monthsDiff;


	function __construct($customerData)
	{
		$this->customerData = $customerData;
	}

	/**
	 * Overview chart data generator
	 * 
	 * @return Array 
	 */
	public function getOverviewChartData()
	{

		if( !empty($this->customerData["goal_amount"]) && $this->customerData["goal_amount"] > 0){
			return $this->overviewDataByGoal();
		}

		return $this->overviewDataByDates();
	}

	/**
	 * Pie chart data generator
	 * 
	 * @return Array 
	 */
	public function getPieChartData()
	{
		$data = array();	
		array_push($data, [ 'Asset', 'Investment']);

		foreach ($this->getAssets() as $asset) {
			array_push($data, [ $asset , rand(5, 15)]);
		}

		return $data;
	}

	/**
	 * Get overview chart based on start
	 * and end dates
	 * 
	 * @return Array
	 */
	public function overviewDataByDates()
	{

		$this->monthsDiff = $this->customerData["start"]->diffInMonths($this->customerData["end"]);

		$data = array();

		$startDate = $this->customerData["start"];

		$step = $this->getStepForDates();

		$stepsLoop = intval($this->monthsDiff / $step);

		array_push($data, [ 'Month', 'Average Market Performance', 'Expected Market Performance', 'Poor Market Performance']);

		$average = intval($this->customerData["investment_amount"]);
		$expected = intval($this->customerData["investment_amount"]);
		$poor = intval($this->customerData["investment_amount"]);

		$precentUpper = $this->getPrecentByRiskForDates();

		for($i = 0 ; $i <= $stepsLoop ; $i++){

			$expected *= 1 + $precentUpper / 100;

			$startDate->addMonths($step);

		}

		$this->customerData["goal_amount"] = $expected;

		return $this->overviewDataByGoal();
	}

	/**
	 * Get overview chart based on goal amount
	 * 
	 * @return Array
	 */
	public function overviewDataByGoal()
	{
		$data = array();

		$startDate = Carbon::now();

		$goal = intval($this->customerData["goal_amount"]);

		$step = $this->getStepForGoal();

		array_push($data, [
			"name" => "Poor Market Performance",
			"color" => "#FF9800", 
			"lineWidth"	=>	1,
			"zIndex" => 2,
			"fillOpacity" => 0.3,
			"data" => [] 
		]);
		array_push($data, [
			"name" => "Average Market Performance",
			"color" => "#9E9E9E", 
			"lineWidth"	=>	1,
			"zIndex" => 1,
			"fillOpacity" => 0.3, 
			"data" => [] 
		]);
		array_push($data, [
			"name" => "Expected Market Performance",
			"color" => "#2196F3", 
			"lineWidth"	=>	1,
			"zIndex" => 0,
			"fillOpacity" => 0.3, 
			"data" => [] 
		]);

		//$poor = 1000;//intval($this->customerData["investment_amount"]);
		//$average = 1000;//intval($this->customerData["investment_amount"]);
		//$expected = 1000;//intval($this->customerData["investment_amount"]);

		//$precentUpper = $this->getPrecentByRiskForGoal();

		$goalReached = false;
		$i = 8;
		while( !$goalReached ){

			//$stepData = [ $startDate->format("M y"), $average, $expected, $poor ];

			//$poor *= 1 -  ($precentUpper[0] / 100);//($step * $precentUpper[0]) / 100;
			//$average *= 1 + ($precentUpper[1] / 100);//($step * $precentUpper[1]) / 100;
			//$expected *= 1 + ($precentUpper[2] / 100);//($step * $precentUpper[2]) / 100;

			$poor = intval(exp($i));
			$average = intval(exp($i * 1.13));
			$expected = intval(exp($i * 1.2));

			array_push( $data[0]["data"], [$startDate->timestamp, $poor] );
			array_push( $data[1]["data"], [$startDate->timestamp, $average] );
			array_push( $data[2]["data"], [$startDate->timestamp, $expected] );

			$i += 0.15;

			$startDate->addMonths(1);


			if($expected >= intval($this->customerData["goal_amount"]) ){
				//dd($data);
				break;
			}

		}

		return $data;
	}

	/**
	 * determine the step every month
	 * based on the risk level
	 * 
	 * @return int
	 */
	public function getStepForGoal()
	{
		switch ( $this->customerData["risk"] ) {
			case 'conservative':

				if( $this->addCheckGoal(21) )
					return 1;

				if( $this->addCheckGoal(42) )
					return 2;
				
				return 4;

				break;

			case 'moderate':
				
				if( $this->addCheckGoal(29) )
					return 1;

				if( $this->addCheckGoal(58) )
					return 2;
				
				return 4;

				break;

			case 'aggressive':
				
				if( $this->addCheckGoal(44) )
					return 1;

				if( $this->addCheckGoal(88) )
					return 2;
				
				return 4;

				break;
			
			default:

				if( $this->addCheckGoal(21) )
					return 1;

				if( $this->addCheckGoal(42) )
					return 2;
				
				return 4;

				break;
		}
	}

	/**
	 * check if investment amount is on term
	 * 
	 * @return bool
	 */
	public function addCheckGoal($precent)
	{
		return !! ( intval($this->customerData["investment_amount"]) * ( 1 + $precent / 100 ) > intval($this->customerData["goal_amount"]));
	}

	public function getStepForDates()
	{

		if( $this->monthsDiff <= 7 ){
			return 1;
		}

		if( $this->monthsDiff > 7 && $this->monthsDiff <= 14){
			return 2;
		}

		if( $this->monthsDiff > 14 ){
			return 3;
		}
	}

	/**
	 * determine the precentage risk based
	 * on plan and risk type
	 * 
	 * @return Array
	 */
	public function getPrecentByRiskForDates()
	{
		switch ( $this->customerData["risk"] ) {
			case 'conservative':

				return ($this->monthsDiff / 12 * 35) / $this->monthsDiff;
				break;

			case 'moderate':
				
				return ($this->monthsDiff / 12 * 49) / $this->monthsDiff;
				break;

			case 'aggressive':
				
				return ($this->monthsDiff / 12 * 75) / $this->monthsDiff;
				break;
			
			default:
				
				return ($this->monthsDiff / 12 * 75) / $this->monthsDiff;
				break;
		}
	}

	/**
	 * determine the step every month
	 * based on the risk level
	 * 
	 * @return int
	 */
	public function getPrecentByRiskForGoal()
	{
		switch ( $this->customerData["risk"] ) {
			case 'conservative':
				return [1, 1.6, 2];
				break;

			case 'moderate':
				return [1, 2, 3.2];
				break;

			case 'aggressive':
				return [1, 3, 5];
				break;
			
			default:
				return [1, 3, 5];
				break;
		}
	}

	/**
	 * generate a list of assets to iterate
	 * 
	 * @return Array
	 */
	public function getAssets()
	{
		return [
			'Major Assets', 'Oil / Energy', 'Forex', 'Comodeties', 'Indecies', 'Gold'
		];
	}
}