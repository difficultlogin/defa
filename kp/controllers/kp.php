<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kp extends MY_Controller {
    # Type rate

    private $type_rate = array(
        'fixed' => array(
            'name'     => 'Фиксированный',
            'value'    => 'fixed',
            'template' => 'kp_seo_fixed.xlsx',
        ),
        'top_10' => array(
            'name'     => 'ТОП 10',
            'value'    => 'top_10',
            'template' => 'kp_seo_top_10.xlsx',
        ),
        'traffic' => array(
            'name'     => 'По трафику',
            'value'    => 'traffic',
            'template' => 'kp_seo_traffic.xlsx',
        )
    );

    # Directory path

    private $template_path = '/var/www/manager/data/www/8-u.ru/resources/kp/template/';

    function __construct() {
        parent::__construct();

        # Check access

        $this->load->model('clients/clients_mdl', 'clients_mdl');

        $data['has_access_all'] = $this->clients_mdl->has_access_all();

        if ($data['has_access_all']['finance'] != 1) {
            echo 'Доступ запрещен';
            die();
        }

        # Load model kp_mdl for all method

        $this->load->model('kp_mdl');
    }

    public function index() {
        $data = array();

        # Get items / filter

        if ($this->input->post('manager') && $this->input->post('manager') !== 'all') {
            $filter_array = array(
                array(
                    'name'  => 'userid',
                    'value' =>  $this->input->post('manager'),
                ),
            );

            $items_kp = $this->kp_mdl->get_items_filter($filter_array);
            $manager_filter = $this->input->post('manager');
        } else {
            $items_kp = $this->kp_mdl->get_items();
            $manager_filter = 'all';
        }

        # Items kp data

        $data['items_kp']       = $items_kp;
        $data['rates']          = $this->type_rate;
        $data['managers']       = $this->kp_mdl->get_managers();
        $data['manager_filter'] = $manager_filter;

        # Meta data

        $data_meta = array();
        $data_meta['title'] = 'Коммерческие предложения';

        # View result

        $this->_view_page('index', $data, $data_meta);
    }

    public function edit($id) {
        if ($this->input->post() && $result = $this->validate_form($this->input->post())) {
            $this->kp_mdl->edit($id, $result);

            echo json_encode(array('success', ''));

            return false;
        }

        # Prepare data

        $item = $this->kp_mdl->get_item($id);

        # Data item kp

        $data                  = array();
        $data['kp']            = $item;
        $data['regions_promo'] = $this->kp_mdl->get_all_regions();
        $data['rates']         = $this->type_rate;
        $data['managers']      = $this->kp_mdl->get_managers();
        $data['optional_data'] = json_decode($item['data'], TRUE);

        unset($data['kp']['data']);

        # Meta data

        $data_meta = array();
        $data_meta['title'] = 'Редактирование КП';

        # View result

        $this->_view_page('form', $data, $data_meta);
    }

    public function add() {
        if ($this->input->post() && $result = $this->validate_form($this->input->post())) {
            $id = $this->kp_mdl->add($result);

            echo json_encode(array('success', ''));

            $manager_data = $this->kp_mdl->get_user($result['manager']);

            $to       = $manager_data['login'] . '@pr-up.ru';
            $subject  = 'КП по ' . $result['site_name'];
            $message  = 'Вы можете скачать отчет по этой <a href="http://8-u.ru/kp/download/'.$id.'">ссылки</a>';
            $message .= '<br><h2>Пояснительная записка</h2>'.$result['kp_description'];
            $name     = $manager_data['name'];

            $this->load->model('tools/email_mdl');
            $this->email_mdl->send($to, $subject, $message, true, array(), $name);

            return false;
        }

        # Item data

        $data = array();
        $data['regions_promo'] = $this->kp_mdl->get_all_regions();
        $data['rates']         = $this->type_rate;
        $data['managers']      = $this->kp_mdl->get_managers();

        # Meta data

        $data_meta = array();
        $data_meta['title'] = 'Создание нового КП';

        # View result

        $this->_view_page('form', $data, $data_meta);
    }

    public function remove($id) {
        $this->kp_mdl->remove($id);

        echo json_encode(array('success'));
    }

    public function download($id) {
        $data = $this->kp_mdl->get_item($id);

        $this->generate_xls($data);
    }

    private function validate_form($data) {
        $result = array();
        $option_data = '';

        # Main fields

        if (!isset($data['site_name']) || !$data['site_name']) {
            echo json_encode(array('error', 'Не заполнено поле Название сайта'));
            exit();
        } else {
            $site_name = $this->validate_site_name($data['site_name']);

            if (!$site_name) {
                echo json_encode(array('error', 'Домен введен некорректно'));
                exit();
            } else {
                $result['site_name'] = $site_name;
            }
        }

        if (!isset($data['search_machine'])) {
            echo json_encode(array('error', 'Выберите хотя бы одну поисковую систему'));
            exit();
        } else {
            $result['search_machine'] = json_encode($data['search_machine']);
        }

        if (!isset($data['region_promotion'])) {
            echo json_encode(array('error', 'Выберите регион продвижения'));
            exit();
        } else {
            $result['region_promotion'] = $data['region_promotion'];
        }

        if (!isset($data['rate'])) {
            echo json_encode(array('error', 'Выберите тарифный план'));
            exit();
        } else {
            $result['rate'] = $data['rate'];
        }

        if (!isset($data['manager'])) {
            echo json_encode(array('error', 'Выберите менеджера'));
            exit();
        } else {
            $result['manager'] = $data['manager'];
        }

        if (isset($data['kp_description'])) {
            $result['kp_description'] = $data['kp_description'];
        }

        # Fixed fields

        if ($data['rate'] == 'fixed') {
            if (!isset($data['subscription']) || !$data['subscription']) {
                echo json_encode(array('error', 'Укажите абонентскую плату'));
                exit();
            } else {
                $option_data['subscription'] = $data['subscription'];
            }
        }

        # Top 10 field

        if ($data['rate'] == 'top_10') {
            if (!isset($data['top10_count_month']) || !$data['top10_count_month']) {
                echo json_encode(array('error', 'Укажите количество месяцов'));
                exit();
            } else {
                $option_data['top10_count_month'] = $data['top10_count_month'];
            }

            if (!isset($data['top10_coefficient']) || !$data['top10_coefficient']) {
                echo json_encode(array('error', 'Укажите коэффициент'));
                exit();
            } else {
                $option_data['top10_coefficient'] = $data['top10_coefficient'];
            }

            if (!isset($data['top10_first_month']['subscription']) || !$data['top10_first_month']['subscription']) {
                echo json_encode(array('error', 'Укажите абонентскую плату за первый период'));
                exit();
            } else {
                $option_data['top10_first_month']['subscription'] = $data['top10_first_month']['subscription'];
            }

            if (isset($data['top10_first_month']['premium'])) {
                $option_data['top10_first_month']['premium'] = $data['top10_first_month']['premium'];
            }

            if (!isset($data['top10_after_month']['subscription']) || !$data['top10_after_month']['subscription']) {
                echo json_encode(array('error', 'Укажите абонентскую плату за второй период'));
                exit();
            } else {
                $option_data['top10_after_month']['subscription'] = $data['top10_after_month']['subscription'];
            }

            if (isset($data['top10_after_month']['premium'])) {
                $option_data['top10_after_month']['premium'] = $data['top10_after_month']['premium'];
            }

            if (!isset($data['top10_after_month']['max_limit']) || !$data['top10_after_month']['max_limit']) {
                echo json_encode(array('error', 'Укажите максимальный лимит ежемесячного платежа'));
                exit();
            } else {
                $option_data['top10_after_month']['max_limit'] = $data['top10_after_month']['max_limit'];
            }

            if (!isset($data['top10_after_month']['timeline_limit']) || !$data['top10_after_month']['timeline_limit']) {
                echo json_encode(array('error', 'Укажите срок действия лимита платежа'));
                exit();
            } else {
                $option_data['top10_after_month']['timeline_limit'] = $data['top10_after_month']['timeline_limit'];
            }
        }

        # Traffic fields

        if ($data['rate'] == 'traffic') {
            if (!isset($data['traffic_count_month']) || !$data['traffic_count_month']) {
                echo json_encode(array('error', 'Укажите количество месяцов'));
                exit();
            } else {
                $option_data['traffic_count_month'] = $data['traffic_count_month'];
            }

            if (!isset($data['traffic_first_month']['subscription']) || !$data['traffic_first_month']['subscription']) {
                echo json_encode(array('error', 'Укажите абонентскую плату за первый период'));
                exit();
            } else {
                $option_data['traffic_first_month']['subscription'] = $data['traffic_first_month']['subscription'];
            }

            if (isset($data['traffic_first_month']['premium'])) {
                $option_data['traffic_first_month']['premium'] = $data['traffic_first_month']['premium'];
            }

            if (!isset($data['traffic_after_month']['subscription']) || !$data['traffic_after_month']['subscription']) {
                echo json_encode(array('error', 'Укажите абонентскую плату за второй период'));
                exit();
            } else {
                $option_data['traffic_after_month']['subscription'] = $data['traffic_after_month']['subscription'];
            }

            if (isset($data['traffic_after_month']['premium'])) {
                $option_data['traffic_after_month']['premium'] = $data['traffic_after_month']['premium'];
            }

            if (!isset($data['traffic_after_month']['price_one_click']) || !$data['traffic_after_month']['price_one_click']) {
                echo json_encode(array('error', 'Укажите цену одного перехода'));
                exit();
            } else {
                $option_data['traffic_after_month']['price_one_click'] = $data['traffic_after_month']['price_one_click'];
            }

            if (!isset($data['traffic_after_month']['max_limit']) || !$data['traffic_after_month']['max_limit']) {
                echo json_encode(array('error', 'Укажите максимальный лимит ежемесячного платежа'));
                exit();
            } else {
                $option_data['traffic_after_month']['max_limit'] = $data['traffic_after_month']['max_limit'];
            }

            if (!isset($data['traffic_after_month']['timeline_limit']) || !$data['traffic_after_month']['timeline_limit']) {
                echo json_encode(array('error', 'Укажите срок действия лимита платежа'));
                exit();
            } else {
                $option_data['traffic_after_month']['timeline_limit'] = $data['traffic_after_month']['timeline_limit'];
            }
        }

        # Data for semantic

        if (!isset($data['data_export']) || !$data['data_export']) {
            echo json_encode(array('error', 'Загрузите файл семантического ядра (.csv)'));
            exit();
        } else {
            $result['data_export'] = $data['data_export'];
        }

        $result['optional_data'] = json_encode($option_data);
        return $result;
    }

    # Helper functions

    public function validate_site_name($site_name) {
        # Remove https:// | http:// | www
        # Input = https://example.com | Output = example.com

        return $site_name;
    }

    public function report() {
        # Convert data from csv file to array

        $result = array();

        if(!setlocale(LC_ALL, 'ru_RU.utf8')) setlocale(LC_ALL, 'en_US.utf8');

        if (isset($_FILES['semantic_kernel'])) {
            $handle = fopen($_FILES['semantic_kernel']['tmp_name'], 'r');

            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                $result[] = $data;
            }
        }

        echo json_encode($result);
    }

    private function generate_xls($data) {
        # Prepare data

        $postfix_price = ' руб.';
        $rate = $data['rate'];
        $export_data = json_decode($data['data_export'], TRUE);
        $option_data = json_decode($data['data'], TRUE);
        $template = $this->type_rate[$rate]['template'];

        # Generate XLS

        $this->load->library('excel');

        # Styles XLS

        $style_table_default = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array(
                        'rgb' => '000000'
                    )
                )
            ),
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'indent' => 1,
            ),
        );

        $style_table_first_elem = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                )
            ),
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'indent' => 1,
            ),
        );

        $style_table_phrase = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                    'color' => array(
                        'rgb' => '000000'
                    )
                )
            ),
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'indent' => 1,
            ),
        );

        $style_table_last_elem = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                )
            ),
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'indent' => 1,
            ),
        );

        # Read template XLS

        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($this->template_path.$template);

        # Write Value

        # Main List

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('E11', ' '.$data['site_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E13', ' '.$data['region_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E12', '');
        $objPHPExcel->getActiveSheet()->setCellValue('E14', ' «'.$this->type_rate[$rate]['name'].'»');

        # Paste logo search

        $search_machine = json_decode($data['search_machine'], TRUE);
        $search_machine_logo = new PHPExcel_Worksheet_Drawing();

        if (count($search_machine) == 2) {
            $search_machine_logo->setPath($this->template_path.'img/y_g.png');
        } else {
            if ($search_machine[0] == 'yandex') {
                $search_machine_logo->setPath($this->template_path.'img/y.png');
            } else {
                $search_machine_logo->setPath($this->template_path.'img/g.png');
            }
        }

        $search_machine_logo->setCoordinates('E12');
        $search_machine_logo->setResizeProportional(FALSE);
        $search_machine_logo->setWorksheet($objPHPExcel->getActiveSheet());
        $search_machine_logo->setOffsetX(5);
        $search_machine_logo->setOffsetY(8);

        # Date Created

        $date_created = date('d.m.Y', $data['date_create']);
        $date_validity = date('d.m.Y', strtotime("{$date_created} + 20 days"));

        $objPHPExcel->getActiveSheet()->setCellValue('E8', 'Дата расчета: '.$date_created);
        $objPHPExcel->getActiveSheet()->setCellValue('E9', 'Предложение действительно до '.$date_validity);

        $objPHPExcel->getActiveSheet()->getStyle('E9')->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffff99')
            )
        ));

        # Cost List | Fixed

        if ($rate == 'fixed') {
            $objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->setCellValue('E9', ' '.$option_data['subscription'] . $postfix_price);
        }

        # Cost List | TOP 10

        if ($rate == 'top_10') {
            $objPHPExcel->setActiveSheetIndex(1);

            $objPHPExcel->getActiveSheet()->setCellValue('B12', ' Первые '.$option_data['top10_count_month'].' месяца:');
            $objPHPExcel->getActiveSheet()->setCellValue('B16', ' После '.$option_data['top10_count_month'].' месяцев:');
            $objPHPExcel->getActiveSheet()->setCellValue('E13', ' '.$option_data['top10_first_month']['subscription'] . $postfix_price);

            if (isset($option_data['top10_first_month']['premium'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('E14', ' Да');
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue('E14', ' Нет');
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E17', ' '.$option_data['top10_after_month']['subscription'] . $postfix_price);

            if (isset($option_data['top10_after_month']['premium'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('E18', ' Да');
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue('E18', ' Нет');
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E19', ' '.$option_data['top10_after_month']['max_limit'] . $postfix_price);
            $objPHPExcel->getActiveSheet()->setCellValue('E20', ' '.$option_data['top10_after_month']['timeline_limit'] . ' месяцев');
        }

        # Cost List | Traffic

        if ($rate == 'traffic') {
            $objPHPExcel->setActiveSheetIndex(1);

            $objPHPExcel->getActiveSheet()->setCellValue('B12', ' Первые '.$option_data['traffic_count_month'].' месяца:');
            $objPHPExcel->getActiveSheet()->setCellValue('B16', ' После '.$option_data['traffic_count_month'].' месяцев:');
            $objPHPExcel->getActiveSheet()->setCellValue('E13', ' '.$option_data['traffic_first_month']['subscription'] . $postfix_price);

            if (isset($option_data['traffic_count_month']['premium'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('E14', ' Да');
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue('E14', ' Нет');
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E17', ' '.$option_data['traffic_after_month']['subscription'] . $postfix_price);

            if (isset($option_data['traffic_after_month']['premium'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('E18', ' Да');
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue('E18', ' Нет');
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E19', ' '.$option_data['traffic_after_month']['price_one_click'] . $postfix_price);
            $objPHPExcel->getActiveSheet()->setCellValue('E20', ' '.$option_data['traffic_after_month']['max_limit'] . $postfix_price);
            $objPHPExcel->getActiveSheet()->setCellValue('E21', ' '.$option_data['traffic_after_month']['timeline_limit'] . ' месяцев');
        }

        # Semantic kernel List

        $objPHPExcel->setActiveSheetIndex(2);

        array_shift($export_data); # remove first line (headers)

        $count_column = count($export_data[0]) + 2;

        if ($rate == 'top_10') {
            $count_column = 7;
        }

        $row = 1;
        for ($i = 15, $k = 0; $i < count($export_data) + 15; $i++, ++$k) {
            for ($j = 2, $y = 0; $j < $count_column; $j++, ++$y) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $row); # number

                # Fixed

                if ($rate == 'fixed') {
                    if ($y == 1 || $y == 2) {
                        # Set position | changes

                        if ($export_data[$k][$y] <= 0 || $export_data[$k][$y] > 100) {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, '> 100');
                        } else {
                            $value = explode(',', $export_data[$k][$y]);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $value[0]);
                        }
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $export_data[$k][$y]);
                    }
                }

                # TOP 10

                if ($rate == 'top_10') {

                    if ($y == 0 || $y == 3) {
                        # Set phrase & frequency | without changes

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $export_data[$k][$y]);
                    } elseif ($y == 1 || $y == 2) {
                        # Set position | changes

                        if ($export_data[$k][$y] <= 0 || $export_data[$k][$y] > 100) {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, '> 100');
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $export_data[$k][$y]);
                        }
                    } elseif ($y == 4) {
                        # Set cost | changes

                        $new_price = $export_data[$k][$y] * $option_data['top10_coefficient'];
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $new_price);
                    }

                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($count_column - 2, $i)->applyFromArray(array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'indent' => 1,
                        ),
                    ));
                }

                # Traffic

                if ($rate == 'traffic') {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $export_data[$k][$y]);
                }

                # Set style

                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i)->applyFromArray($style_table_default);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1, $i)->applyFromArray($style_table_first_elem);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($count_column - 1, $i)->applyFromArray($style_table_last_elem);
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2, $i)->applyFromArray($style_table_phrase);
            }
            $row++;
        }

        # About company List

        $objPHPExcel->setActiveSheetIndex(5);

        $user = $this->kp_mdl->get_user($data['userid']);
        $user_phone = $user['mobile_phone'];
        $user_email = $user['login'] . '@pr-up.ru';

        $objPHPExcel->getActiveSheet()->setCellValue('B42', $user['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('B46', $user_phone);
        $objPHPExcel->getActiveSheet()->setCellValue('B45', '+7 (499) 638-25-17'. ' доб. ' . $user['additional_number']);
        $objPHPExcel->getActiveSheet()->setCellValue('B48', $user_email);

        # Set Default active tab

        $objPHPExcel->setActiveSheetIndex(0);

        # Write and Put result XLS
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Promo Group. КП SEO '.$this->type_rate[$rate]['name'].'. '.$data['site_name'].'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}