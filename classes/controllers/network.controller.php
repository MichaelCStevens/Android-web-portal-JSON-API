<?php

class networkController extends router {

    public $page;
    public $data = array();
    public $range;
    public $dataSource;
    public $dataType;
    public $format_type = 0;
    public $rangeType;

    function __construct() {
        $this->format_type = 0;
        require_once router::$fileRoot . "classes/models/network.model.php";
        $this->model = new networkModel;
        $this->processInput();
    }

    public function processInput() {
        if ($_POST) {
            $this->dataType = filter_input(INPUT_POST, 'data_type', FILTER_UNSAFE_RAW);
            $this->format_type = filter_input(INPUT_POST, 'format_type', FILTER_UNSAFE_RAW);
            $this->range = filter_input(INPUT_POST, 'range', FILTER_UNSAFE_RAW);
            $this->rangeType = filter_input(INPUT_POST, 'range_type', FILTER_UNSAFE_RAW);
            $this->dataSource = filter_input(INPUT_POST, 'data_source', FILTER_UNSAFE_RAW);
            //  echo"datatype = $this->dataType";
           // print_r(array('datatype'=>$this->dataType, 'range'=>$this->range, 'rangetype'=>$this->rangeType, 'datatsource'=>$this->dataSource));
            if ($this->format_type == 1) {
                $this->data['risk_data'] = $this->model->getChartData($this->dataType, $this->range, $this->rangeType, $this->dataSource, 'risk');
                //  print_r($this->data['risk_data']);
            } elseif ($this->format_type == 2) {
                $this->data['chart_data'] = $this->model->getChartData($this->dataType, $this->range, $this->rangeType, $this->dataSource, 'chart');
                // print_r($this->data['chart_data'] );
            }
        }
    }

}
