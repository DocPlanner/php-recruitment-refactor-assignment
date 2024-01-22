# Refactoring & unit testing challenge


## The task

Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). Refactor the code preserving the functionality and add unit tests using your favourite framework.

The business requirements are not given, you need to reverse-engineer them from the code. There are no hidden bugs (as far as we know), you don't have to focus on fixing the behaviour, but rather on refactoring the code and proving using unit tests that it is correct.

The aim is to use unit testing, but if you'd like to propose a solution using integration tests, we're curious about it as well. You will have a chance to explain your reasoning later.

## Installation
Only the vendor API is dockerized and configured to work with `docker-compose`. However, feel free to dockerize the rest of the project if you find it helpful.

To run the container, use `docker-compose up -d`.
After a while, the vendor API serving doctors will be accessible on `http://localhost:2137`.

## Hints

- It’s up to you to decide on how much time you will spend. However, please don’t invest too much effort.
- If you have any issue or something is unclear, don't hesitate to ask
