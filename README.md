
# Docplanner Code Challenge

## The task

Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). Add unit tests using your favourite framework to test its business responsibilities. Feel free to refactor any part of the code as you see fit.

The business requirements are not given, you need to reverse-engineer them from the code. There are no hidden bugs (as far as we know), you don’t have to focus on fixing the behaviour, but rather on proving using unit tests that it is correct.

The aim is to use unit testing, but if you’d like to propose a solution using integration tests, we’re curious about it as well. You will have a chance to explain your reasoning later.

## Time constraints

It should take between 2 and 4 hours to complete this challenge, depending on your pace. We will not measure your time, it's only for your information, so you can estimate the required effort.

## Installation
The project is dockerized and configured to work with `docker-compose`.

To run the container, use `docker-compose up -d`
After a while, the vendor API serving doctors will be accessible on `http://localhost:2137` as you will see in the code.

