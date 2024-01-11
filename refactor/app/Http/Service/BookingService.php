<?php

namespace DTApi\Service\Controllers;

use DTApi\Repository\BookingRepository;
use DTApi\Repository\UserRepository;

class BookingService
{
    /**
     * @var BookingRepository
     */
    protected $booking;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(
        BookingRepository $bookingRepository,
        UserRepository $userRepository
    ){
        $this->booking = $bookingRepository;
        $this->user = $userRepository;
    }

    /**
     * get bookings for booking page
     */
    public function getBookings($request){
        /**
         * initizize response
         */
        $response = '';

        /**
         * if user id exist
         */
        if($user_id = $request->get('user_id')) {

            /**
             * get user information by user id
             */
            $user_info = $this->user->identifyUserByID($user_id);

            /**
             * if there is user info found
             */
            if($user_info){
                /**
                 * identify user type as per user detail
                 */
                $usertype = ($user_info->is('customer')? 'customer' : ($user_info->is('translator') ? 'translator' : ''));

                /**
                 * get jobs by user type and user id
                 */
                $jobs = $this->booking->getJobsByUser($usertype, $user_info);

                $emergencyJobs = [];
                $noramlJobs = [];

                /**
                 * if there are jobs found for the user
                 */
                if($jobs){
                    foreach ($jobs as $jobitem) {
                        if ($jobitem->immediate == 'yes') {
                            $emergencyJobs[] = $jobitem;
                        } else {
                            $noramlJobs[] = $jobitem;
                        }
                    }

                    /**
                     * elaborate noramlJobs information
                     */
                    $noramlJobs = $this->booking->normalJobsDetails($noramlJobs);

                }
                
                $response = ['emergencyJobs' => $emergencyJobs, 'noramlJobs' => $noramlJobs, 'cuser' => $user_info, 'usertype' => $usertype];

            }

        } elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')){
            /**
             * if user_type is either admin or superadmin
             */
            $response = $this->booking->getAll($request);
        }

        return $response;
    }

    /**
     * get specific job details by job id
     */
    public function getSpecificJob($id){
        return $this->booking->with('translatorJobRel.user')->find($id);
    }

    /**
     * save booking details as per authenticated user
     */
    public function saveBookingDetails($user, $data){
        return $this->booking->store($user, $data);
    }

    /**
     * update job details as per id
     */
    public function updateBookingInfoById($id, $data, $user){
        return $this->booking->updateJob($id, $data, $user);
    }

    /**
     * save job email address
     */
    public function saveJobEmail($data){
        return $this->booking->storeJobEmail($data);
    }

    /**
     * get job history as per user id
     */
    public function getJobHistoryByUserId($request){
        $response = null;
        if($user_id = $request->get('user_id')) {
            /**
             * initilize values
             */
            $usertype = '';
            $emergencyJobs = [];
            $noramlJobs = [];

            /**
             * get page by request
             */
            $page = $request->get('page');
            $pagenum = (isset($page) ? $page : "1");

            /**
             * get user info as per user id
             */
            $user_info = $this->user->identifyUserByID($user_id);

            /**
             * identify user type as per user detail
             */
            $usertype = ($user_info->is('customer')? 'customer' : ($user_info->is('translator') ? 'translator' : ''));

            /**
             * if user type is customer
             */
            if($user_info->is('customer')){
                /**
                 * get jobs history as per user info
                 */
                $jobs_history = $this->booking->getjobsHistoryforCustomer($user_info);

                return [
                    'emergencyJobs' => $emergencyJobs,
                    'noramlJobs' => $noramlJobs,
                    'jobs' => $jobs_history,
                    'cuser' => $user_info,
                    'usertype' => $usertype,
                    'numpages' => 0,
                    'pagenum' => 0
                ];
            }

            if($user_info->is('translator')){
                /**
                 * get job history for translator 
                 */
                $jobs_history = $this->booking->getJobsHistoryForTranslator($user_info, $pagenum);

                return [
                    'emergencyJobs' => $emergencyJobs,
                    'noramlJobs' => $jobs_history,
                    'jobs' => $jobs_history,
                    'cuser' => $user_info,
                    'usertype' => $usertype,
                    'numpages' => ceil($jobs_history->total() / 15),
                    'pagenum' => $pagenum
                ];
            }
        }

        /**
         * return blank array if no user id is in the request
         */
        return [];
    }

    /**
     * accept job as per User Auth
     */
    public function acceptJobByUserAuth($data, $user, $type){
        if($type == 'wojobid'){
            return $this->booking->acceptJob($data, $user);


        }

        if($type == 'wjobid'){
            return $this->booking->acceptJobWithId($data, $user);
        }
    }

    /**
     * Cancel job
     */
    public function cancelJob($data, $user){
        return $this->booking->cancelJobAjax($data, $user);
    }

    /**
     * End job
     */
    public function endJob($data){
        return $this->booking->endJob($data);
    }

    /**
     * Customer not call
     */
    public function customerNotCall($data){
        return $this->booking->customerNotCall($data);
    }

    /**
     * pull potential jobs available
     */
    public function pullPotentialJobs($user){
        return $this->booking->getPotentialJobs($user);
    }

    /**
     * enable distance feed
     */
    public function distanceFeed($data){

        /**
         * identify distance value
         */
        $distance = (isset($data['distance']) && $data['distance'] != "" ? $data['distance'] : "");

        /**
         * identify time value
         */
        $time = (isset($data['time']) && $data['time'] != "" ? $data['time'] : "");

        /**
         * identify session value
         */
        $session = (isset($data['session_time']) && $data['session_time'] != "" ? $data['session_time'] : "");

        /**
         * identify if job is manually handled
         */
        $manually_handled = ($data['manually_handled'] == 'true' ? 'yes' : 'no');

        /**
         * identify if action is by admin
         */
        $by_admin = ($data['by_admin'] == 'true' ? 'yes' : 'no');

        /**
         * identfy admin comment
         */
        $admincomment = (isset($data['admincomment']) && $data['admincomment'] != "" ? $data['admincomment'] : "");

        /**
         * identify job id
         */
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        /**
         * identify if job is flagged
         */
        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        /**
         * if there is time and distance, update distance by job id
         */
        if ($time || $distance) {
            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        /**
         * if action is by admin, update job
         */
        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));
        }

        return 'Record updated!';
    }

    /**
     * reopen Booking
     */
    public function reOpenBooking($data){
        return $this->booking->reopen($data);
    }

    /**
     * resend booking notification
     */
    public function reSendNotif($data){
        $job = $this->booking->find($data['jobid']);
        $job_data = $this->booking->jobToData($job);
        $this->booking->sendNotificationTranslator($job, $job_data, '*');

        return ['success' => 'Push sent'];
    }

    /**
     * resend sms notification
     */
    public function reSendSMSNotif($data){
        $job = $this->booking->find($data['jobid']);
        $job_data = $this->booking->jobToData($job);

        try {
            $this->booking->sendSMSNotificationToTranslator($job);
            return ['success' => 'SMS sent'];
        } catch (\Exception $e) {
            return ['success' => $e->getMessage()];
        }
    }




}
