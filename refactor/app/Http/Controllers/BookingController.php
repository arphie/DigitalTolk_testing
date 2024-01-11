<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Service\BookingService;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(
        Request $request,
        BookingService $booking
    ){
        /**
         * get jobs to show in the 
         */
         $response = $booking->getBookings($request);

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show(
        $id,
        BookingService $booking
    ){
        /**
         * get specific job details
         */
        $job = $booking->getSpecificJob($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(
        Request $request,
        BookingService $booking
    ){
        /**
         * save booking details
         */
        $response = $booking->saveBookingDetails($request->__authenticatedUser, $request->all());

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update(
        $id,
        Request $request,
        BookingService $booking
    ){
        /**
         * update booking info by booking id
         */
        $response = $booking->updateBookingInfoById($id, array_except($request->all(), ['_token', 'submit']), $request->__authenticatedUser);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(
        Request $request,
        BookingService $booking
    ){
        /**
         * save job email by id
         */
        $response = $booking->saveJobEmail($request->all());

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(
        Request $request,
        BookingService $booking
    ){
        /**
         * get job history as per user id
         */
        $response = $booking->getJobHistoryByUserId($request);
        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(
        Request $request,
        BookingService $booking
    ){
        /**
         * accept job w/o job id in mind
         */
        $response = $booking->acceptJobByUserAuth($request->all(), $request->__authenticatedUser, 'wojobid');

        return response($response);
    }

    /**
     * accept job with ID
     */
    public function acceptJobWithId(
        Request $request,
        BookingService $booking
    ){
        /**
         * accept job with job id in mind
         */
        $response = $booking->acceptJobByUserAuth($request->get('job_id'), $request->__authenticatedUser, 'wjobid');

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->cancelJob($request->all(), $request->__authenticatedUser);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->endJob($request->all());

        return response($response);

    }

    /**
     * update job, customer no call
     */
    public function customerNotCall(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->customerNotCall($request->all());

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->pullPotentialJobs($request->__authenticatedUser);

        return response($response);
    }

    /**
     * distance feed
     */
    public function distanceFeed(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->distanceFeed($request->all());

        return response($response);
    }

    /**
     * reopen job booking
     */
    public function reopen(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->reOpenBooking($request->all());
        return response($response);
    }

    /**
     * resend notification 
     */
    public function resendNotifications(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->reSendNotif($request->all());

        return response($response);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(
        Request $request,
        BookingService $booking
    ){
        $response = $booking->reSendSMSNotif($request->all());
        return response($response);
    }

}
