<?php


namespace App\Services;


use App\Entity\Event;
use App\Entity\EventArchive;
use App\Entity\State;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Date;

class EventService
{
    public function updateStateEvent(EntityManager $em){
        $string = "";

        try {
            $eventRepository = $em->getRepository(Event::class);
            $stateRepository = $em->getRepository(State::class);
            $events = $eventRepository->findAll();

            foreach ($events as $event) {
                $date = $event->getDate();
                $now = new \DateTime();
                $limitDate =   new $now;

                $limitDate->modify('-1 month');
                if ($date instanceof \DateTime && $limitDate instanceof \DateTime) {

                    if ($date < $limitDate) {
                        $event->setState($stateRepository->findBy(['denomination' => 'Cloturée'])[0]);
                        $eventArchive = new EventArchive($event);
                        $em->persist($eventArchive);
                        $em->remove($event);
                        $string .= $event . " archive ";

                    } elseif ($date == $now) { //don't working because it's DateTime
                        $event->setState($stateRepository->findBy(['denomination' => 'Activité en cours'])[0]);
                        $em->persist($event);
                        $string .= $event . " active ";

                    } elseif ($date < $now) {
                        $event->setState($stateRepository->findBy(['denomination' => 'Passée'])[0]);
                        $em->persist($event);
                        $string .= $event . " past ";
                    }
                }
            }
            $em->flush();

        } catch (\Exception $e) {
            $string .= $e->getMessage();
        }
        return $string;
    }

}