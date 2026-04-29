---
name: ultrathink
description: Use when an implementation plan exists and you need deep critical analysis of edge cases, security, performance, architecture compliance, and failure modes before writing code
---

# Ultrathink — Deep Critical Analysis

Perform a structured critical analysis of the design spec and implementation plan against the actual codebase before any code is written.

## When to Use

- After `superpowers:writing-plans` has produced an implementation plan
- Before `superpowers:test-driven-development` begins
- Can also be invoked standalone on any existing plan: `/ultrathink`

## Inputs

1. **Design spec** — from brainstorming (`docs/superpowers/specs/YYYY-MM-DD-<topic>-design.md`)
2. **Implementation plan** — from writing-plans (`docs/superpowers/plans/YYYY-MM-DD-<feature-name>.md`)
3. **Codebase** — targeted exploration of files referenced in the plan

## Process

### Step 1: Load Context

Read the design spec and implementation plan. Identify all files, models, migrations, and components referenced in the plan.

### Step 2: Targeted Codebase Exploration

Use Explore agents (max 3 in parallel) to examine:
- Files that will be modified (current state, dependencies)
- Related existing patterns (how similar features were built)
- CLAUDE.md conventions that apply

### Step 3: Run the 6 Analysis Dimensions

For each dimension, examine every task in the plan and document findings with severity ratings.

#### 3.1 Edge Case Analysis
Walk through each plan task and identify inputs, states, or conditions that could cause unexpected behavior.
- Empty/null data states
- Concurrent state conflicts (Livewire/Alpine)
- Boundary conditions
- Race conditions

#### 3.2 Security Implications
- Authentication/authorization gaps (guard crossover, role checks)
- Data exposure between tenants/users
- File upload validation
- SQL injection via raw queries
- XSS in user-generated content or `contenteditable` editors
- CSRF protection

#### 3.3 Performance Considerations
- N+1 queries in new Eloquent relationships
- Missing database indexes on new columns
- Heavy polling intervals
- Large file handling
- Query complexity on large tables

#### 3.4 Architecture Compliance
Check against CLAUDE.md conventions:
- CSS prefix uniqueness
- Livewire single-root-element rule
- Alpine.js `@entangle().live` patterns
- `wire:ignore` for Alpine-managed DOM
- Session-based tab visibility (3-place update rule)
- Custom sidebar integration
- Fixed-position drawer placement (root level)

#### 3.5 Migration & Dependency Risk
- Compatibility with existing migrations
- `foreignId()->constrained()` vs `unsignedBigInteger()` decision
- MySQL 64-char index name limits
- Impact on scheduled commands
- New package dependencies

#### 3.6 Failure Mode Analysis
- External integration failures (Twilio, MS Graph, AWS, etc.)
- Partial save scenarios
- WebSocket disconnection mid-operation
- Graceful degradation paths

### Step 4: Write Analysis Document

Save findings to `docs/superpowers/ultrathink/YYYY-MM-DD-<feature-name>-analysis.md`:

```markdown
# Ultrathink Analysis: [Feature Name]
Date: YYYY-MM-DD

## Summary Verdict
[GO / GO WITH CHANGES / STOP — needs redesign]

## Edge Cases Identified
- [Severity: LOW/MEDIUM/HIGH/CRITICAL] Description — Recommended mitigation

## Security Review
- [Severity] Finding — Mitigation

## Performance Concerns
- [Severity] Concern — Estimated impact — Mitigation

## Architecture Compliance
- [PASS/FAIL] Convention — Details

## Migration & Dependency Risks
- [Severity] Risk — Fallback strategy

## Failure Modes
- [Severity] Scenario — Graceful degradation recommendation

## Plan Amendments Required
- [ ] Specific change to implementation plan (with task reference)
```

### Step 5: Gate Decision

Based on findings:

| Verdict | Condition | Action |
|---------|-----------|--------|
| **GO** | No HIGH/CRITICAL findings | Proceed to TDD |
| **GO WITH CHANGES** | Has HIGH findings with clear mitigations | Amend the plan document, then proceed to TDD (no re-run) |
| **STOP** | Has CRITICAL findings or fundamental design flaws | Return to brainstorming for redesign |

Present the verdict and analysis summary to the user for confirmation before proceeding.

## Anti-Patterns

- **"The plan looks fine"** — Every plan has something. Dig deeper.
- **Broad codebase scanning** — Only explore files referenced in the plan. Stay targeted.
- **Re-running after GO WITH CHANGES** — One pass is sufficient. Amend and move on.
- **Duplicating brainstorming** — Brainstorming asks "what should we build?" Ultrathink asks "what could go wrong with how we plan to build it?"
