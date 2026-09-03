# Visiotech TAASCOR reference inventory

## Scope

The user designated `https://taascor.visiotechsolutions.com/` as an additional public TAASCOR reference on 2026-09-03. The homepage, About page, and Contact page were inspected in an actual browser. This record separates reusable TAASCOR-owned identity/content from template, stock, vendor, or staging material that should not be copied into the production candidate without further approval.

## Integrated locally

| Element | Source | Local destination | Treatment |
| --- | --- | --- | --- |
| TMGS hexagon mark | `https://taascor.visiotechsolutions.com/wp-content/uploads/2025/02/sitelogo-1-1.png` | `assets/brand/taascor-mark.png` | Original-resolution public asset retained. Used in all primary lockups and favicon derivatives. |
| TAASCOR legal-entity wordmark | `https://taascor.visiotechsolutions.com/wp-content/uploads/2025/02/TAASCOR.png` | `assets/brand/taascor-wordmark.reference.png` | Original public asset retained as evidence. |
| Website wordmark reference | Same source | `assets/brand/taascor-wordmark.png` | Cropped only to remove the separate “powered by VisioTech Solutions” vendor line. Retained as a reference derivative, not used as the responsive header surface because the source image has an opaque white background. |
| Responsive transparent lockup | Original TMGS mark plus the exact legal-name text visible in the reference wordmark | `index.html`, `site/bootstrap.php`, `app/views/header.php` | Uses the authentic transparent mark and live HTML text for the TAASCOR name and legal subtitle. This preserves the identity while allowing the surrounding light or dark navigation background to remain continuous. |
| Favicon/touch icons | Derived from the original TMGS mark | `favicon-32.png`, `icon-192.png`, `apple-touch-icon.png` | Proportionally resized onto transparent square canvases; no mark redrawing or recoloring. |
| Tagline | Visiotech homepage: “Powering Business with the Right People” | Existing homepage title and hero | Already present; source now corroborated. |
| Legal entity and SEC reference | Visiotech About page | Existing About company profile | Already integrated from the supplied company profile; source now corroborated. |

## Inspected but not copied

- The homepage technology/innovation copy is generic template language and does not describe the full TAASCOR workforce offer with enough precision.
- The homepage and Contact photography is third-party stock imagery without a local retained-license record.
- The footer social links point to generic Facebook and Hostinger social accounts, not verified TAASCOR profiles.
- The Visiotech-subdomain mailbox may be a staging/developer address and is not treated as the final public company channel.
- Mission and vision labels on the Visiotech About page conflict with the fuller company-profile source supplied by the user; the approved fuller source remains authoritative.
- The Contact-page address and telephone were inventoried but require reconciliation with the seven-office register before publication as canonical contact details.

## Asset integrity

| File | SHA-256 |
| --- | --- |
| `taascor-mark.png` | `650861B561A7D3E32AAC60FE7AD28359FF8F628F7A397846CCA9CBDE0CEFBBC7` |
| `taascor-wordmark.reference.png` | `CA62BD4C77FF5E45A2E78393655E7F318C5DE10F80D47DBDD2B7CD6C1543BDF9` |
| `taascor-wordmark.png` | `491D1D3C99CE8E7BB1C2ADC4B55629D307D2293FFB642F9A315D46A1C9E98708` |

## Publication boundary

The user's designation authorizes local use of the public reference. Final release still requires the normal brand/content review and exact-commit deployment checks; it does not convert template photography, placeholder social links, or staging contact details into approved production facts.
