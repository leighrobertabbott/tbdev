# Code Style Guide for TorrentBits 2025

This document outlines the coding standards and style guidelines for the TorrentBits 2025 project.

## General Principles

- Follow PSR-12 coding standards for PHP
- Maintain readability and consistency
- Write self-documenting code with clear variable and function names
- Keep files and functions focused and modular

## PHP Standards

### Line Length

**Maximum line length: 80 characters**

Lines should not exceed 80 characters. When a line would exceed this limit:

1. **Method Calls**: Split parameters onto multiple lines
   ```php
   // Good
   $router->get(
       '/path',
       [Controller::class, 'method'],
       'route.name'
   );
   
   // Bad
   $router->get('/path', [Controller::class, 'method'], 'route.name', [Middleware::auth()]);
   ```

2. **Array Definitions**: Break arrays across multiple lines
   ```php
   // Good
   $config = [
       'key1' => 'value1',
       'key2' => 'value2',
       'key3' => 'value3',
   ];
   
   // Bad
   $config = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
   ```

3. **String Concatenation**: Split at concatenation operators
   ```php
   // Good
   $message = 'This is a long string ' .
       'that needs to be split ' .
       'across multiple lines';
   
   // Bad
   $message = 'This is a very long string that exceeds the 80 character limit';
   ```

4. **Method Chaining**: Break chains at each method
   ```php
   // Good
   $result = $query
       ->where('status', 'active')
       ->orderBy('created_at', 'desc')
       ->limit(10)
       ->get();
   
   // Bad
   $result = $query->where('status', 'active')->orderBy('created_at', 'desc')->limit(10)->get();
   ```

5. **SQL Queries**: Break at SQL keywords
   ```php
   // Good
   $sql = "SELECT * FROM users " .
       "WHERE status = 'active' " .
       "AND created_at > '2025-01-01' " .
       "ORDER BY username";
   
   // Bad
   $sql = "SELECT * FROM users WHERE status = 'active' AND created_at > '2025-01-01' ORDER BY username";
   ```

### Indentation

- Use 4 spaces for indentation (no tabs)
- Align multi-line statements with the opening delimiter

### Naming Conventions

- **Classes**: PascalCase (e.g., `UserController`, `TorrentModel`)
- **Methods**: camelCase (e.g., `getUserById()`, `validateInput()`)
- **Variables**: camelCase (e.g., `$userId`, `$torrentList`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_FILE_SIZE`, `DB_HOST`)
- **Database Tables**: snake_case (e.g., `user_torrents`, `forum_posts`)

### Documentation

- Add PHPDoc blocks for all classes and public methods
- Include `@param`, `@return`, and `@throws` tags where applicable
- Keep inline comments concise and meaningful

```php
/**
 * Retrieve a user by their ID
 *
 * @param int $userId The user's unique identifier
 * @return User|null The user object or null if not found
 * @throws DatabaseException If database connection fails
 */
public function getUserById(int $userId): ?User
{
    // Implementation
}
```

### Type Hints

- Use strict types: `declare(strict_types=1);`
- Add type hints for parameters and return types
- Use nullable types when appropriate (`?string`, `?int`)

### Error Handling

- Use exceptions for error conditions
- Catch specific exceptions rather than generic `Exception`
- Log errors appropriately

## HTML/View Templates

### Line Length

Views may exceed 80 characters for HTML elements with multiple classes or attributes. Aim for readability over strict line length.

### Indentation

- Use 4 spaces for PHP templates
- Maintain consistent indentation within HTML structure

### PHP in Templates

- Use short echo syntax: `<?= $variable ?>` instead of `<?php echo $variable; ?>`
- Keep logic minimal - complex logic belongs in controllers
- Extract reusable components into partials

## JavaScript/Frontend

### Line Length

Maximum 80 characters where possible

### Style

- Use modern ES6+ syntax
- 2 spaces for indentation
- Use `const` and `let`, avoid `var`
- Use template literals for string interpolation

## CSS/Tailwind

### Organization

- Use Tailwind utility classes for styling
- Custom CSS should be minimal and well-documented
- Group related utility classes logically

### Line Length

CSS class attributes may exceed 80 characters for complex layouts. Break at logical boundaries when possible.

## SQL

### Formatting

- Uppercase SQL keywords: `SELECT`, `FROM`, `WHERE`, `JOIN`
- Break long queries at clauses
- Indent subqueries and joins

```sql
SELECT
    u.id,
    u.username,
    COUNT(t.id) as torrent_count
FROM users u
LEFT JOIN torrents t ON t.user_id = u.id
WHERE u.status = 'active'
GROUP BY u.id, u.username
ORDER BY torrent_count DESC
LIMIT 10;
```

## Tools and Automation

### PHP-CS-Fixer

Run PHP-CS-Fixer to automatically format code:

```bash
# Check formatting
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix formatting
vendor/bin/php-cs-fixer fix
```

### Line Length Checker

A custom script is available to identify and fix common line length violations:

```bash
php scripts/fix_line_lengths.php
```

### EditorConfig

The project includes an `.editorconfig` file. Ensure your editor supports EditorConfig for automatic formatting.

## Git Commit Messages

- Use present tense: "Add feature" not "Added feature"
- First line: brief summary (50 chars or less)
- Add detailed description after blank line if needed
- Reference issues: "Fixes #123" or "Closes #456"

## Testing

- Write tests for new features and bug fixes
- Maintain test coverage
- Use descriptive test names that explain what is being tested

## Security

- Validate and sanitize all user input
- Use prepared statements for database queries
- Implement CSRF protection for forms
- Use proper authentication and authorization checks
- Keep dependencies updated

## Performance

- Avoid N+1 queries
- Use database indexes appropriately
- Cache where beneficial
- Optimize asset loading (minify, compress)

## Version Control

- Keep commits focused and atomic
- Don't commit generated files or dependencies
- Use `.gitignore` appropriately
- Review changes before committing

## Code Review Checklist

Before submitting code for review, ensure:

- [ ] Code follows PSR-12 standards
- [ ] Lines do not exceed 80 characters (where practical)
- [ ] All functions have type hints
- [ ] PHPDoc comments are present and accurate
- [ ] No unused imports or variables
- [ ] Tests pass
- [ ] No security vulnerabilities introduced
- [ ] Code is self-documenting with clear naming

## Resources

- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PHP: The Right Way](https://phptherightway.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [EditorConfig](https://editorconfig.org/)

## Future Work

### Remaining Line Length Violations

As of the last audit, there are approximately 2,128 line length violations remaining across 165 files:

- **Views (86 files, ~1,580 violations)**: HTML templates with long Tailwind CSS class strings
- **Controllers (41 files, ~300 violations)**: Complex method calls and conditionals
- **SQL Queries**: Long database queries that need refactoring
- **Comments and Strings**: Long text strings and documentation

These will be addressed incrementally as files are touched during development. The focus should be on:

1. Views: Consider component extraction and class grouping
2. Controllers: Refactor complex logic into service methods
3. SQL: Move complex queries to dedicated repository classes
4. Strings: Break long strings at logical boundaries

---

*This style guide is a living document and may be updated as the project evolves.*
