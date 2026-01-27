# Refactoring & Architecture Challenge

## It's great you're here!
We're excited to have you at this stage of the recruitment process — and we're looking forward to seeing how you think
and approach complex problems.

This task is designed to evaluate your ability to work with AI-assisted development tools (like Cursor) 
while demonstrating senior-level skills in software design, architecture, testing, and business understanding.

We encourage you to use AI tools (Cursor, GitHub Copilot, etc.) as you would in your daily work — but remember, 
the quality of your architectural decisions and understanding of the business logic is what we're evaluating.

## What we're looking for?
We're looking for a Senior developer who is able to work closely with business and understand the business logic and 
purpose behind it. Someone who can leverage modern tools effectively while maintaining high standards of code quality 
and architectural thinking.

This exercise will give you space to demonstrate:

- Proficiency in refactoring legacy or procedural code into clean, maintainable OOP components.
- Your ability to deeply understand and translate complex business requirements into well-structured, maintainable, 
  and valuable software solutions.
- Strong understanding and application of SOLID principles.
- Use of appropriate design patterns where they add clarity and value.
- Awareness and thoughtful application of modern software architecture principles, clearly separating technical 
  concerns while maintaining deep business insight.
- A pragmatic yet rigorous approach to unit testing, emphasizing the testability of business-critical components.
- Clear, developer-friendly communication—whether through your code structure, naming conventions, or thoughtful commentary.
- Being up to date with PHP language development and its features.
- **Ability to work with AI tools effectively** — knowing when to use them, how to guide them, and when to override their suggestions.
- **Architectural decision-making** — understanding trade-offs and making informed choices.

## Business Context

You're working on a **Doctor Slots Synchronization System** for Docplanner. This system:

- Synchronizes doctor information and their available appointment slots from external vendor APIs
- Must handle multiple data sources (currently one, but designed to scale)
- Needs to be resilient to API failures and network issues
- Must maintain data consistency and handle edge cases in real-world scenarios
- Will be extended in the future with additional features (notifications, analytics, etc.)

## Task description

### Phase 1: Understanding & Refactoring (Required)
Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). 

Your initial goals:
- Clearly articulate and understand the existing business logic purely from the provided code.
- Refactor the class to make it easier to test, reason about, and maintain. Think in terms of architecture, 
  modularity, and clarity.
- Write comprehensive tests for the extracted/refactored business logic using your preferred PHP testing framework.
- Document your architectural decisions (consider using ADRs - Architecture Decision Records, or at least inline comments).

### Phase 2: New Business Requirements (Required)

After refactoring, implement the following new features:

#### 2.1 Multi-Source Synchronization
- The system now needs to support **multiple vendor APIs** (not just one)
- Each vendor may have different:
  - Authentication methods (some use Basic Auth, others might use API keys, OAuth, etc.)
  - Response formats (though they'll be normalized to the same structure)
  - Rate limits and retry policies
- Design the architecture to easily add new vendors without modifying existing code

#### 2.2 Enhanced Error Handling & Resilience
- Implement a **retry mechanism** with exponential backoff for failed API calls
- Add **circuit breaker pattern** to prevent cascading failures when a vendor API is down
- Implement proper **error categorization** (transient vs permanent errors)
- Add **dead letter queue** concept for slots that fail to sync after multiple retries
- Log errors with appropriate context for debugging

#### 2.3 Slot Conflict Detection
- Detect and handle **slot conflicts** (overlapping slots for the same doctor)
- When conflicts are detected:
  - Log them with detailed information
  - Apply a conflict resolution strategy (your choice, but document it)
  - Consider: should we merge, reject, or flag for manual review?

#### 2.4 Data Validation & Business Rules
- Validate that slots make business sense:
  - Start time must be before end time
  - Slot duration must be within reasonable bounds (e.g., 15 minutes to 4 hours)
  - Slots cannot be in the past (or handle historical data appropriately)
  - Doctor names must follow certain patterns (extend the existing normalization)
- Add validation for doctor data integrity

#### 2.5 Performance & Scalability Considerations
- The system may need to sync **thousands of doctors** with **hundreds of slots each**
- Implement **batching** or **chunking** strategies to avoid memory issues
- Consider **async processing** capabilities (you can use a simple queue interface, no need for full implementation)
- Add basic **caching** for vendor API responses where appropriate

#### 2.6 Observability & Monitoring
- Add structured logging with different log levels
- Implement basic **metrics collection** (count of synced doctors, slots, errors, etc.)
- Add **health check endpoints** or methods to verify system status
- Consider adding **tracing** for debugging synchronization flows

### Phase 3: Documentation & Communication (Required)

- **Architecture Documentation**: Create a document explaining your architectural decisions
- **API Documentation**: Document any new interfaces or public APIs you create
- **Testing Strategy**: Document your testing approach and coverage
- **Deployment Considerations**: Add notes about how this would be deployed and monitored in production

### Phase 4: Optional Enhancements (Bonus Points)

If you have time and want to go further, consider:

- **Event-Driven Architecture**: Implement domain events for slot creation/updates
- **CQRS Pattern**: Separate read and write models if it makes sense
- **Dockerization**: Complete dockerization of the entire application
- **CI/CD Pipeline**: Add GitHub Actions or similar for automated testing
- **Performance Testing**: Add load tests or benchmarks
- **Additional Vendor Implementation**: Implement a second mock vendor with different characteristics

## Technical Requirements

- Use **PHP 8.1+** features (typed properties, enums, match expressions, etc.)
- Follow **PSR standards** (PSR-4, PSR-12)
- Write **comprehensive tests** (aim for >80% coverage on business logic)
- Use **dependency injection** properly
- Consider **immutability** where it makes sense
- Use **value objects** for domain concepts where appropriate

## Evaluation Criteria

We'll evaluate:

1. **Code Quality**: Clean, readable, maintainable code
2. **Architecture**: Well-structured, scalable, follows SOLID principles
3. **Business Understanding**: Correct implementation of business rules
4. **Testing**: Comprehensive, meaningful tests
5. **Documentation**: Clear explanations of decisions and trade-offs
6. **Tool Usage**: Effective use of AI tools (not just copy-paste, but thoughtful application)
7. **Problem Solving**: How you handle edge cases and unexpected scenarios

## Additional Notes & Tips

- **AI Tools**: Feel free to use Cursor, GitHub Copilot, ChatGPT, etc. We're evaluating your ability to work WITH these tools, not without them. However, we expect you to:
  - Understand and review all generated code
  - Make architectural decisions yourself
  - Refactor and improve AI suggestions when needed
  - Document why you made certain choices

- **Time Management**: Focus on:
  - Quality over quantity
  - Clear architecture over quick hacks
  - Understanding trade-offs
  - Documenting your thinking

- **Questions**: If anything is unclear, don't hesitate to reach out — asking questions is a good sign.

- **Scope**: You don't need to implement everything perfectly. If something would take too long:
  - Implement a simplified version
  - Leave clear TODOs with explanations
  - Document what the full implementation would look like

## How to use this repository?

### Getting Started
- To create your copy, 
  - Use the green "Use this template" button on the top right. It should be set as private repo. 
  - Do not use the Fork feature.
- Complete the task as described above, following the phases.
- When done, give access to the repo to the hiring manager and other people provided.
- Send us the link to the PULL REQUEST **in your repo**, so we can review your work.

### Submission Guidelines
- Create a **single pull request** with all your changes
- Use **meaningful commit messages** that explain what and why
- Include a **summary** in the PR description explaining:
  - Your architectural approach
  - Key decisions you made
  - What you prioritized and why
  - Any trade-offs you considered
  - How you used AI tools (if you're comfortable sharing)

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Docker and Docker Compose (for the vendor API)

### Setup Steps

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Start the vendor API:**
   ```bash
   docker-compose up -d
   ```
   After a while, the vendor API serving doctors will be accessible on `http://localhost:2137`.

3. **Test the API is working:**
   ```bash
   curl -u docplanner:docplanner http://localhost:2137/api/doctors
   ```

4. **Run tests** (after you set up your testing framework):
   ```bash
   # Example with PHPUnit
   vendor/bin/phpunit
   ```

### Vendor API Endpoints

The vendor API provides the following endpoints:

- `GET /api/doctors` - Returns list of doctors (requires Basic Auth: docplanner/docplanner)
- `GET /api/doctors/{id}/slots` - Returns slots for a specific doctor

**Note:** The API may occasionally return errors or be slow to simulate real-world conditions. Your implementation should handle these gracefully.

## Project Structure

```
src/
├── Entity/              # Domain entities (Doctor, Slot)
├── DoctorSlotsSynchronizer.php  # Original code to refactor
└── StaticDoctorSlotsSynchronizer.php  # Helper for testing

# After refactoring, you might want to organize like:
src/
├── Entity/              # Domain entities
├── Service/             # Business logic services
├── Repository/          # Data access layer
├── Vendor/              # Vendor API integrations
├── Exception/           # Custom exceptions
├── ValueObject/         # Value objects
└── ...
```

## Tips for Success

1. **Start Small**: Begin with Phase 1 (refactoring), get tests passing, then move to Phase 2
2. **Iterate**: Don't try to implement everything at once. Refactor, test, then add features
3. **Document as You Go**: Write comments and documentation while the context is fresh
4. **Use AI Wisely**: Let AI help with boilerplate, but make architectural decisions yourself
5. **Test Early**: Write tests as you refactor, not after
6. **Think About Production**: Consider how this would run in a real environment

## Questions?

If anything is unclear, don't hesitate to reach out. We value:
- Clear communication
- Thoughtful questions
- Understanding trade-offs
- Pragmatic solutions

## Good luck!

We're excited to see what you build! 🚀
