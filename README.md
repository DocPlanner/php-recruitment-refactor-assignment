# Refactoring & unit testing challenge

## How to use this repsitory and complete the assignment
Hello! First of all - welcome and congrats that we can meet on this stage of the process! 

Below you will find the home assignment we prepared for you. Some guidelines:
1. To create your copy, Use the green "Use this template" button on the top right. It should be set as private repo. Do not use Fork feature.
2. Complete the task as described below.
3. When done, give access to the repo to the hiring manager and other people provided.
4. Send us the link to the PULL REQUEST in your repo, so we can review your work. 

Good luck!


## Task description

Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). Add unit tests using your favourite framework to test its business logic. Refactor any part of the code if you find it useful for architectural and testing purposes.

The business requirements are not given, you need to reverse-engineer them from the code. There are no hidden bugs (as far as we know), you don't have to focus on fixing the behaviour, but rather on refactoring the code and proving using unit tests that it is correct.

The aim is to use unit testing, but if you'd like to propose a solution, we're curious about it as well.

## Installation
Only the vendor API is dockerized and configured to work with `docker-compose`. However, feel free to dockerize the rest of the project if you find it helpful.

To run the container, use `docker-compose up -d`.
After a while, the vendor API serving doctors will be accessible on `http://localhost:2137`.

## Hints

- Show us your skills.
- Remember to write tests that test business logic, not implementation.
- It's up to you how much time you want to spend.
- If you could do something better, but it's too much work, please put a comment on what you would improve.
- If something is unclear, don't hesitate to ask.
