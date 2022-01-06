<?php

namespace App\Services;

class StaticPushNotificationMessageService
{
    /**
     * %1 payment gateway name
     * %2 ride share company name
     * %3 discount percentage or flat rate
     * %4 amount
     * %5 how many trip
     * %6 how many day or offer last date
     * %7 driver name
     * %8 driver total rating
     * %9 pickup time min/hours
     * %10 system generated 4 digit pin number
     * %11 car type
     * %12 car reg. number
     * %13 rider name
    **/
    public static $TripLifeCycle = array(
        'digital_payment' => array('title' => 'Stay safer with contactless Payment', 'message' => 'Pay with %1$s on your next %2$s trip & get %3$s off upto %4$s. Valid for %5$s trips till %6$s'),
        'trip_accepted' => array('title' => 'Your driver is on the way', 'message' => '%7$s (%8$s stars) will arrived in %9$s'),
        'send_pin' => array('title' => 'Use PIN %10$s to verify your ride', 'message' => 'Share your Pin, in person, when your driver arrives'),
        'arriving_driver' => array('title' => 'Your Driver is arriving', 'message' => '%7$s is arriving now in a %11$s (%12$s).'),
        'arrived_driver' => array('title' => 'Your driver has arrived', 'message' => 'Your driver has arrived near you!'),
        'verify_pin' => array('title' => 'Ride verified', 'message' => 'Have a great trip!'),
        'trip_end' => array('title' => 'Please pay your driver %4$s ', 'message' => 'You can view more details in the app.'),
        'provide_rating' => array('title' => 'Rate your trip', 'message' => 'Thanks for riding with %7$s! Please rate your trip.'),
        'rating_finish' => array('title' => 'About your last %2$s trip', 'message' => '%13$s, Thank you for riding with us! Please fill a short feedback survey to tell us about your trip experience.')
    );
}