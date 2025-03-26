# Refactoring & unit testing challenge

## It's great you're here!
We're excited to have you at this stage of the recruitment process — and we're looking forward to seeing how you think
and approach complex problems.

This task is designed not only to evaluate your ability to write clean and testable code, but more importantly 
to **understand your approach to software design, architecture, and testing at a senior level.**

## What we're looking for?
This exercise will give you space to demonstrate:

- Proficiency in refactoring legacy or procedural code into clean, maintainable OOP components.
- Strong understanding and application of SOLID principles.
- Use of appropriate design patterns where they add clarity and value.
- Awareness of domain-driven design and separation of concerns. 
- A pragmatic but thoughtful approach to unit testing and testability. 
- Clear, developer-friendly communication — whether it's through code, structure, or comments.
- Being up to date with PHP language development and its features.

## Task description
Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). 
Your goal is to:
Understand the business logic based solely on the existing code.

- Refactor the class to make it easier to test, reason about, and maintain. Think in terms of architecture, 
modularity, and clarity.
- Write unit tests for the extracted/refactored business logic using your preferred PHP testing framework.
- If you see opportunities for improvements that go beyond the scope of the task, feel free to:
  - Add them
  - Or simply leave comments describing your suggestions and reasoning

We will very much appreciate comments about why you've chosen certain solutions!

### Additional Notes & Tips
- You decide how much time you want to spend — quality over quantity is key.
- If something could be improved but would take too long, just leave a comment or TODO.
- If anything is unclear, don’t hesitate to reach out — asking questions is a good sign.

## How to use this repository?
- To create your copy, 
  - Use the green "Use this template" button on the top right. It should be set as private repo. 
  -  Do not use Fork feature.
- Complete the task as described above.
- When done, give access to the repo to the hiring manager and other people provided.
- Send us the link to the PULL REQUEST **in your repo**, so we can review your work.

## Installation
Only the vendor API is dockerized and configured to work with `docker-compose`. However, feel free to dockerize the rest of the project if you find it helpful.
To run the container, use `docker-compose up -d`.
After a while, the vendor API serving doctors will be accessible on `http://localhost:2137`.

## Good luck!
