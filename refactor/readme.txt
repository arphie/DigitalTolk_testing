:Change log

* created Service/BookingService
- this will enable the separation of code and segmentize the functionalities.
- service will focus on handling bussiness logic and will prepare the data for the controller

* modify BookingController
- removed all logic and moved it to serices
- converted repository request to service request
- added comment as per method

* modify BookingRepository
- modify methods, moved other logic to services