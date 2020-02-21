<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\Request;

class HomeService
{


    private $start;

    public function __construct()
    {

        $this->start = false;
    }


    public function buildQuery(Request $request, $eventRepo ,$siteRepo ,$user ){

        $filter=" ";
        $siteSelected = $request->query->get('site');
        $searchText = $request->query->get('search_text');
        $dateStart = $request->query->get('date_start');
        $dateEnd = $request->query->get('date_end');
        $organizer = $request->query->get('checkbox_organizer');
        $participate = $request->query->get('checkbox_participate');
        $noParticipate = $request->query->get('checkbox_no_participate');
        $past = $request->query->get('checkbox_past');

        if($siteSelected == null){
            $siteSelected = $siteRepo->find( $user->getSite()->getId());
        }else{
            $siteSelected = $siteRepo->find($siteSelected);;
        }

        $filter .= "e.site = ".$siteSelected->getId()." ";
        if($searchText != null || $searchText != ""){
            $filter .= "AND e.name LIKE '%".$searchText."%' ";
        }
        if(($dateStart != null || $dateStart != "") && ($dateEnd != null || $dateEnd != "")){
            $filter .= "AND e.date >= '".$dateStart."' AND e.date <= '".$dateEnd."' ";
        }
        $filter .= "AND ( " ;
        if($past){
            $filter .="e.date < CURRENT_TIMESTAMP()  ";
        }else{
            $filter .="e.date >= CURRENT_TIMESTAMP()  ";
        }
        if($organizer){

           $this->addPrefix($filter,"e.organizer = ".$user->getId()." ");
        }
        if($participate){
            $this->addPrefix($filter,"u = ".$user->getId()." ");
        }
        if($noParticipate){
            $this->addPrefix($filter,"(u != ".$user->getId()." OR u IS NULL)");
        }
        $filter .= ") " ;

        return $eventRepo->findWithFilter($filter);

    }

    /**
     * @param $filter add OR execpt the first time
     */
    private function addPrefix($filter , $string){
        if (!$this->start)
            $filter .= "OR ".$string;
        else
            $filter .= $string;
        return $filter;

    }


}