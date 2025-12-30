1. Purpose

This document defines hard technical boundaries for the CRM OS codebase.

Any feature, PR, migration, or integration that violates this document:

❌ Must not be merged

❌ Must not be deployed

❌ Must be rejected at design review

This prevents:

Feature creep

One-off customer logic

Unmaintainable customization

SaaS scalability failure

-------------------------------------
2. Core Architectural Invariants (NON-NEGOTIABLE)

2.1 Multi-CRM Architecture

A User Account may own multiple CRMs

Each CRM:

Has isolated data

Has independent feature toggles

Has exactly one industry preset

Data must never cross CRM boundaries

Required column on all business tables:

crm_id (UUID, indexed, non-null)


No table may rely on user_id for isolation.
-------------------------------------

2.2 Module System (Feature Toggles)

Every non-core capability must be a module.

Rules

Modules must be:

Toggleable per CRM

Enforced at backend + frontend

A disabled module:

Must not expose routes

Must not expose UI

Must not accept writes

Forbidden

Feature flags without backend enforcement

Hard-coded feature availability
-------------------------------------

2.3 Industry Presets

Presets are configuration only.

Presets may control

Enabled modules

Default pipelines

Field visibility

Terminology labels

Dashboard widgets

Presets may NOT

Introduce new database tables

Introduce custom logic branches

Override permissions logic

-------------------------------------

3. Customization Constraints
3.1 Custom Fields

Allowed field types ONLY:

string

text

number

boolean

date

select (enum)

Rules

Max 30 custom fields per entity

No formulas

No references to other entities

No computed fields

-------------------------------------

3.2 No Custom Logic

The system must not support:

User-defined scripts

Conditional expressions

Formula evaluation

Rule builders beyond presets

All business logic must live in versioned backend code.

-------------------------------------

4. Explicitly Forbidden Domains

The following domains must not appear in the codebase:

Domain	Status
Accounting engines	❌
Payroll calculations	❌
Tax logic	❌
Social posting	❌
ERP workflows	❌
Messaging platforms	❌
Plugin execution	❌

Integrations are read-only or event-based only.
-------------------------------------

5. WordPress Boundary Rules

WordPress acts as Control Plane only.

Allowed

Billing status

Plan limits

Feature toggles

Account suspension

Support impersonation

Forbidden

Business data writes

CRM business logic

Direct CRM DB access

All WP → CRM communication must go through authenticated APIs.
-------------------------------------

6. Permissions Model (Mandatory)
6.1 Role System

Roles are fixed:

Owner

Admin

Manager

User

Read-only

Forbidden

Custom roles

Permission editing by customers
-------------------------------------

6.2 Data Access Rules

All queries must enforce:

WHERE crm_id = :current_crm


“My data only” visibility must be enforced server-side
-------------------------------------

7. Inventory / Dealer Module Constraints
Allowed

Vehicles

Aircraft

Equipment

Forbidden

Cost accounting

Depreciation logic

Manufacturing workflows

Warehouse management

Inventory may only be:

Associated to Deals

Associated to Customers

Tracked by status
-------------------------------------
8. Automation (When Implemented)

Allowed:

Event → Action mapping

Predefined triggers

Predefined actions

Forbidden:

Arbitrary branching

Nested workflows

User-authored logic

External code execution
-------------------------------------

9. API & Integration Rules
Allowed

Webhooks (outbound)

Zapier / Make

Read-only imports

Forbidden

Inbound logic execution

OAuth scopes beyond read/reporting

Long-lived external dependencies
-------------------------------------

10. Data Safety & Lifecycle
Required

Soft deletes for core entities

Audit logs for:

Deal changes

Ownership changes

Permission changes

Forbidden

Hard deletes without admin override

Cross-CRM bulk operations
-------------------------------------

11. Performance & Scalability Rules

All list queries must be paginated

No N+1 queries

Composite indexes must include crm_id

Background jobs required for:

Imports

Notifications

Syncs
-------------------------------------

12. Pull Request Rejection Checklist

Any PR is rejected if it:

Adds customer-specific logic

Adds untoggleable features

Adds unlimited customization

Introduces cross-CRM access risk

Bypasses permissions

Writes business data from WordPress

Introduces accounting/HR logic
-------------------------------------

13. Golden Rule

If a feature cannot be disabled, it cannot exist.
-------------------------------------

14. Enforcement

Tech Lead approval required for:

New modules

New entities

New integrations

Any violation → revert or rewrite