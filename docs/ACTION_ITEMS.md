# Code Review Action Items

This document tracks specific action items identified during the comprehensive code review.

## Critical Priority (Address Immediately)

### Security

- [ ] **CSRF Protection Audit**
  - Review all 25 forms flagged without apparent CSRF protection
  - Ensure middleware is properly applied to all POST routes
  - Add visible CSRF tokens to forms where missing
  - Files: `views/admin/cleanup/index.php`, `views/admin/bans/create.php`, etc.

- [ ] **Input Validation**
  - Replace direct `$_GET`/`$_POST` usage with Request object
  - Add validation to all user inputs
  - File: `views/admin/settings/index.php` line 9

- [ ] **Rate Limiting**
  - Implement rate limiting on authentication endpoints (`/login`, `/signup`)
  - Add rate limiting to API endpoints
  - Protect against brute force attacks

### Performance

- [ ] **Fix N+1 Query Problem**
  - File: `src/Services/RecommendationService.php`
  - Refactor to use eager loading or batch queries
  - Test performance improvement with large datasets

## High Priority (Next Sprint)

### Database Optimization

- [ ] **Replace SELECT * with Specific Columns**
  - [ ] `src/Services/ActivityService.php`
  - [ ] `src/Services/NotificationService.php`
  - [ ] `src/Services/QueueService.php`
  - [ ] `src/Models/Torrent.php`
  - [ ] And 16 more files (see code review report)
  
- [ ] **Add Database Indexes**
  - Analyze common queries using EXPLAIN
  - Add indexes for foreign keys
  - Add compound indexes for frequently joined columns
  - Document index strategy

### Testing

- [ ] **Set Up Test Framework**
  - Configure PHPUnit
  - Create tests directory structure
  - Add test bootstrap file
  
- [ ] **Write Unit Tests** (Target: 70% coverage)
  - [ ] Core classes (Router, Database, Security)
  - [ ] Models (User, Torrent, Forum)
  - [ ] Services (Authentication, Validation)
  
- [ ] **Write Integration Tests**
  - [ ] Authentication flow
  - [ ] Torrent upload/download
  - [ ] Admin operations

### Code Structure

- [ ] **Split Large Route File**
  - Current: `routes/web.php` (669 lines)
  - Split into:
    - `routes/web/admin.php` - Admin routes
    - `routes/web/user.php` - User/auth routes
    - `routes/web/torrent.php` - Torrent operations
    - `routes/web/forum.php` - Forum routes
    - `routes/web/pages.php` - Static pages

### Security Hardening

- [ ] **Session Configuration**
  - Ensure `session.cookie_httponly = true`
  - Ensure `session.cookie_secure = true` for HTTPS
  - Configure session timeout
  - Document session security settings

- [ ] **Password Policy**
  - Enforce minimum password length (8+ characters)
  - Require mix of character types
  - Implement password strength meter
  - Add breach check (Have I Been Pwned API)

## Medium Priority (Within Month)

### Code Quality

- [ ] **Add strict_types Declarations**
  - Add `declare(strict_types=1);` to all PHP files in src/
  - Estimated: ~170 files need updating

- [ ] **Clean Up Commented Code**
  - `src/Controllers/Tracker/AnnounceController.php` - 30 commented lines
  - Document why code is preserved or remove it

- [ ] **Address TODO/FIXME Comments**
  - [ ] `views/pages/useragreement.php` - 1 TODO
  - [ ] `views/pages/videoformats.php` - 1 TODO
  - [ ] `views/pages/links.php` - 1 TODO
  - Create issues for each TODO and schedule fixes

### Architecture

- [ ] **Implement Repository Pattern**
  - Extract complex queries from models
  - Create repository classes for:
    - TorrentRepository
    - UserRepository
    - ForumRepository
  - Benefit: Better testability and separation of concerns

- [ ] **Extract Long Controller Methods**
  - Identify methods > 50 lines
  - Extract business logic to service layer
  - Keep controllers thin (routing + validation only)

### Documentation

- [ ] **Add PHPDoc to All Public Methods**
  - Include @param, @return, @throws tags
  - Document complex algorithms
  - Add examples for non-obvious usage

- [ ] **Create Architecture Documentation**
  - Document MVC structure
  - Explain request lifecycle
  - Document authentication/authorization flow
  - Add sequence diagrams for key operations

## Low Priority (Nice to Have)

### Development Tools

- [ ] **Set Up Static Analysis**
  - [ ] Configure PHPStan (level 6+)
  - [ ] Configure Psalm
  - [ ] Add to CI pipeline

- [ ] **Code Quality Tools**
  - [ ] Configure PHP_CodeSniffer
  - [ ] Add pre-commit hooks
  - [ ] Add code coverage reporting

### Performance Monitoring

- [ ] **Add Performance Monitoring**
  - Instrument slow queries
  - Track response times
  - Set up alerts for performance degradation

- [ ] **Implement Query Logging**
  - Log slow queries (> 1 second)
  - Analyze and optimize monthly
  - Document optimization results

### Enhancements

- [ ] **Consider ORM Adoption**
  - Evaluate Doctrine or Eloquent
  - Migrate critical models
  - Compare performance

- [ ] **API Documentation**
  - Generate OpenAPI/Swagger docs
  - Add API examples
  - Create Postman collection

- [ ] **Developer Experience**
  - Create CONTRIBUTING.md
  - Add development environment setup
  - Document common tasks
  - Create troubleshooting guide

## Continuous Improvements

### Weekly

- Review new code for security issues
- Run static analysis on changed files
- Update test coverage

### Monthly

- Review slow query log
- Update dependencies
- Security vulnerability scan
- Code quality metrics review

### Quarterly

- Comprehensive code review
- Performance benchmarking
- Security audit
- Technical debt assessment

## Tracking

Use GitHub Issues to track these items:

```bash
# Create issues from this list
# Tag with appropriate labels:
# - security
# - performance  
# - testing
# - documentation
# - refactoring
```

## Progress Tracking

**Created:** 2025-11-24  
**Last Updated:** 2025-11-24  
**Items Completed:** 0 / 60+  
**Next Review Date:** 2025-12-24

---

*This is a living document. Update as items are completed or new issues are discovered.*
