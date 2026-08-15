# Routes And Endpoints

| Method | Route | Purpose |
|---|---|---|
| `GET` | `/` | Server-rendered homepage and all public conversion sections |
| `GET` | `/about` | Company, operating approach and organisations supported |
| `GET` | `/products` | Searchable selected-product catalogue |
| `GET` | `/products/{slug}` | Individual product information and request action |
| `GET` | `/procurement` | Seven-stage procurement journey |
| `GET` | `/quality-compliance` | Documentation-led quality and compliance approach |
| `GET` | `/partners` | Partner and organisation directory |
| `GET` | `/resources` | Approved corporate downloads |
| `GET` | `/faq` | Frequently asked questions |
| `GET` | `/contact` | Request, email, phone, WhatsApp and location options |
| `GET` | `/team/{slug}` | Canonical public team profile |
| `GET` | `/api/challenges/{id}` | One procurement-challenge record |
| `GET` | `/api/categories/{id}` | One product-category record |
| `GET` | `/api/procurement/{id}` | One procurement-process record |
| `GET` | `/api/organizations/{id}` | One supported-organisation record |
| `GET` | `/api/team?department=...` | Public team list, optionally filtered |
| `GET` | `/api/search?q=...` | Search public website content |
| `POST` | `/api/submissions` | Secure quotation, order, feedback, suggestion or support request |

## Static discovery files

- `/robots.txt`
- `/sitemap.xml`

## Customer resources

- `/assets/downloads/company-profile.pdf`
- `/assets/downloads/varenz-capability-presentation.pptx`
- `/assets/downloads/varenz-healthcare-supply-brochure.png`

## Stable homepage targets

`#hero`, `#cta`, `#challenges`, `#categories`, `#featured`, `#procedure`, `#organizations`, `#why`, `#resources`, `#opportunities`, `#team`, `#faq`, and `#location`.
