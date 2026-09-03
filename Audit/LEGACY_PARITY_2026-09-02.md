# TAASCOR legacy-content parity record

## Decision

The current project now has a local implementation path for every material public element observed on `https://taascor.onrender.com/`. Company-profile content has been integrated into the premium public experience instead of copied into a single legacy-style page. Dynamic jobs and personal-data workflows remain governed by the current application rather than being populated from potentially stale legacy records.

## Source coverage

The legacy site was opened in an actual browser on 2026-09-02 after its Render instance completed wake-up. The homepage, client directory, organizational-chart image, navigation, and linked recruitment routes were inventoried without submitting or changing remote data.

## Parity matrix

| Legacy element | Current destination | Implementation status |
| --- | --- | --- |
| Recruitment hero and audience entry points | Homepage, `/jobs/`, `/workforce/`, `/portal/` | Implemented |
| Company background and corporate records | `/about/` | Integrated locally |
| Mission and vision | Homepage and `/about/` | Integrated and correctly labelled from the company-profile version |
| Five detailed core values | Homepage summary and `/about/` editorial sequence | Integrated |
| Six company-profile service families | `/solutions/` operational-service spectrum | Integrated locally |
| Board of Directors and management roles | `/leadership/` | Seven records integrated locally |
| Full organizational chart | `/leadership/#organizational-chart` | Original chart retained, optimized to WebP, and paired with an accessible text view |
| Seven offices and branches | `/locations/` | Addresses and location-specific routing integrated locally |
| 27-company portfolio and brand assets | `/clients/` | All cards and legacy assets integrated locally; jobs remain separate |
| Active job-listing experience | `/jobs/` and clean job-detail routes | Feature complete; production publication still uses the governed current-job source |
| Online application and applicant status | `/apply/`, `/account/`, `/applicant/` | Implemented locally with the safer staged workflow; production collection remains code-locked |
| Staff login and recruitment administration | `/staff/` | Implemented locally with stronger authorization and audit controls; production activation remains code-locked |

## Content boundary

The requesting user explicitly asked for the missing legacy company-profile elements to be included. The integration is local and has not been committed, pushed, or deployed. Before a production release, Corporate, HR, Operations, and relationship/media owners should confirm that leadership appointments, office addresses, registration wording, client relationships, and logo permissions remain current. This gate does not justify removing the content from the local candidate; it identifies the exact business review needed before publication.

## Dynamic-data boundary

The four legacy homepage job cards and job-offer labels attached to client cards were not copied as current vacancies. A historic logo or role label cannot establish that a requisition is open today. The new project already provides search, filters, job detail, application handoff, applicant accounts, staff workflows, and publication controls; approved live roles should enter through that governed source.

## Closure evidence required

- Static source, link, PHP, secret, and asset-reference checks: **12/12 passed**.
- Full browser regression against disposable synthetic data: **30/30 passed in 1.6 minutes**.
- Desktop and 390 px mobile browser review: **passed** for the new homepage, About, Leadership, Locations, Clients, and Solutions surfaces in light and dark themes, with document scroll width equal to client width.
- Asset review: **passed**; the organization chart and all 27 portfolio images load from local WebP assets without broken media.
- Commit/push, release packaging, Hostinger upload, pointer change, and live smoke remain separately authorized steps.
