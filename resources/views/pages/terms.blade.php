@extends('layouts.frontend')
@section('title', 'Terms & Conditions | Migratepay')

@section('content')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
<script src="{{ asset('js/legal-pages.js') }}"></script>

<div class="terms-page">
    <div class="terms-hero">
        <h1>TERMS & CONDITIONS</h1>
        <p>MIGRATEPAY DIGITAL SOLUTIONS</p>
        <div class="terms-meta">Effective Date: 01-07-2026 | Last Updated: 01-07-2026</div>
    </div>

    <div class="terms-container">
        <!-- 3. Mobile TOC Toggle -->
        <div class="mobile-toc-toggle" id="mobileTocToggle">
            <span>☰ Table of Contents</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="tocChevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>

        <!-- Left Sidebar TOC -->
        <aside class="terms-sidebar" id="termsSidebar">
            <div class="toc-title">Table of Contents</div>
            <ul class="toc-list" id="tocList">
                <li><a href="#introduction" class="toc-link">1. Introduction</a></li>
                <li><a href="#definitions" class="toc-link">2. Definitions</a></li>
                <li><a href="#eligibility" class="toc-link">3. Eligibility</a></li>
                <li><a href="#nature-role" class="toc-link">4. Nature and Role of MigratePay</a></li>
                <li><a href="#buyer-responsibilities" class="toc-link">5. Buyer Responsibilities</a></li>
                <li><a href="#seller-responsibilities" class="toc-link">6. Seller Responsibilities</a></li>
                <li><a href="#formation-transaction" class="toc-link">7. Formation of a Transaction</a></li>
                <li><a href="#payment-amount" class="toc-link">8. Payment and Transaction Amount</a></li>
                <li><a href="#platform-fee" class="toc-link">9. Platform Fee</a></li>
                <li><a href="#settlement" class="toc-link">10. Settlement to Seller</a></li>
                <li><a href="#payment-regulatory" class="toc-link">11. Important Payment-Regulatory Provision</a></li>
                <li><a href="#completion-transaction" class="toc-link">12. Completion of a Transaction</a></li>
                <li><a href="#refunds" class="toc-link">13. Refunds</a></li>
                <li><a href="#cancellation" class="toc-link">14. Cancellation</a></li>
                <li><a href="#disputes" class="toc-link">15. Disputes</a></li>
                <li><a href="#fraud-monitoring" class="toc-link">16. Fraud Prevention and Monitoring</a></li>
                <li><a href="#aml-source-funds" class="toc-link">17. Anti-Money Laundering</a></li>
                <li><a href="#prohibited-activities" class="toc-link">18. Prohibited Activities</a></li>
                <li><a href="#account-registration" class="toc-link">19. Account Registration</a></li>
                <li><a href="#kyc-verification" class="toc-link">20. KYC and Verification</a></li>
                <li><a href="#seller-settlement-account" class="toc-link">21. Seller Settlement Account</a></li>
                <li><a href="#taxes-vat" class="toc-link">22. Taxes and VAT</a></li>
                <li><a href="#consumer-rights" class="toc-link">23. Consumer Rights</a></li>
                <li><a href="#seller-compliance" class="toc-link">24. Seller Compliance</a></li>
                <li><a href="#intellectual-property" class="toc-link">25. Intellectual Property</a></li>
                <li><a href="#user-content" class="toc-link">26. User Content</a></li>
                <li><a href="#privacy-data" class="toc-link">27. Privacy and Personal Data</a></li>
                <li><a href="#electronic-communications" class="toc-link">28. Electronic Communications</a></li>
                <li><a href="#transaction-records" class="toc-link">29. Transaction Records</a></li>
                <li><a href="#third-party-providers" class="toc-link">30. Third-Party Service Providers</a></li>
                <li><a href="#third-party-websites" class="toc-link">31. Third-Party Websites</a></li>
                <li><a href="#service-availability" class="toc-link">32. Service Availability</a></li>
                <li><a href="#security" class="toc-link">33. Security</a></li>
                <li><a href="#account-suspension" class="toc-link">34. Account Suspension and Termination</a></li>
                <li><a href="#consequences-termination" class="toc-link">35. Consequences of Termination</a></li>
                <li><a href="#indemnification" class="toc-link">36. Indemnification</a></li>
                <li><a href="#limitation-liability" class="toc-link">37. Limitation of Liability</a></li>
                <li><a href="#no-unlawful-exclusion" class="toc-link">38. No Unlawful Exclusion of Statutory Rights</a></li>
                <li><a href="#force-majeure" class="toc-link">39. Force Majeure</a></li>
                <li><a href="#notices" class="toc-link">40. Notices</a></li>
                <li><a href="#changes-terms" class="toc-link">41. Changes to These Terms</a></li>
                <li><a href="#severability" class="toc-link">42. Severability</a></li>
                <li><a href="#waiver" class="toc-link">43. Waiver</a></li>
                <li><a href="#assignment" class="toc-link">44. Assignment</a></li>
                <li><a href="#relationship" class="toc-link">45. Relationship Between the Parties</a></li>
                <li><a href="#entire-agreement" class="toc-link">46. Entire Agreement</a></li>
                <li><a href="#governing-law" class="toc-link">47. Governing Law</a></li>
                <li><a href="#dispute-resolution" class="toc-link">48. Dispute Resolution</a></li>
                <li><a href="#contact-information" class="toc-link">49. Contact Information</a></li>
                <li><a href="#user-acknowledgement" class="toc-link">50. User Acknowledgement</a></li>
            </ul>
        </aside>

        <!-- Right Content -->
        <div class="terms-content">
            
            <section id="introduction" class="terms-section">
                <h2>1. Introduction</h2>
                <p>Welcome to MigratePay.</p>
                <p>These Terms and Conditions (“Terms”, “Terms and Conditions” or “Agreement”) govern your access to and use of the MigratePay website, platform, applications, software, payment-related facilities, transaction-management services and any other services made available by MigratePay (collectively, the “Platform”).</p>
                <p>By creating an account, accessing the Platform, initiating or participating in a transaction, making a payment, receiving a settlement, or otherwise using any MigratePay service, you acknowledge that you have read, understood and agreed to be legally bound by these Terms.</p>
                <p>If you do not agree to these Terms, you must not use the Platform.</p>
                <p>These Terms constitute an electronic agreement between you and MigratePay to the extent permitted by applicable law.</p>
            </section>

            <section id="definitions" class="terms-section">
                <h2>2. Definitions</h2>
                <p>For purposes of these Terms:</p>
                <ul>
                    <li><strong>“MigratePay”, “we”, “us” or “our”</strong> means MigratePay and the legal entity that owns and operates the Platform.</li>
                    <li><strong>“Platform”</strong> means the MigratePay website, application, software, technology infrastructure, transaction system and related services.</li>
                    <li><strong>“User”, “you” or “your”</strong> means any person or legal entity accessing or using the Platform.</li>
                    <li><strong>“Buyer”</strong> means a User who agrees to purchase or receive goods, services, digital products or other deliverables from a Seller through the Platform.</li>
                    <li><strong>“Seller”</strong> means a User who offers, supplies or sells goods, services, digital products or other deliverables to a Buyer through the Platform.</li>
                    <li><strong>“Transaction”</strong> means a particular transaction entered into between a Buyer and Seller through the Platform.</li>
                    <li><strong>“Transaction Amount”</strong> means the total amount agreed between the Buyer and Seller for a particular Transaction.</li>
                    <li><strong>“Platform Fee”</strong> means the fee payable to MigratePay for providing its applicable platform and transaction-related services.</li>
                    <li><strong>“Settlement”</strong> means the release or transfer of the amount payable to the Seller after completion of the applicable Transaction conditions and after applicable fees, adjustments, deductions or charges.</li>
                    <li><strong>“Refund”</strong> means the return of all or part of an amount paid by a Buyer in connection with a Transaction.</li>
                    <li><strong>“Account”</strong> means a registered MigratePay User account.</li>
                    <li><strong>“Applicable Law”</strong> means all laws, rules, regulations, orders, directives, guidelines, regulatory requirements and legally binding decisions applicable in Bangladesh to the relevant activity.</li>
                </ul>
            </section>

            <section id="eligibility" class="terms-section">
                <h2>3. Eligibility</h2>
                <ol>
                    <li>You must be legally capable of entering into a binding contract under the laws applicable to you.</li>
                    <li>You must provide accurate and complete information when registering or using the Platform.</li>
                    <li>If you are using MigratePay on behalf of a company, partnership, business or other legal entity, you represent that you have the authority to bind that entity to these Terms.</li>
                    <li>MigratePay may require additional identity, business or authorization documents before permitting you to use certain services.</li>
                    <li>MigratePay may refuse, restrict or suspend access where required by Applicable Law or where reasonable security, fraud-prevention or compliance concerns exist.</li>
                </ol>
            </section>

            <section id="nature-role" class="terms-section">
                <h2>4. Nature and Role of MigratePay</h2>
                <ol>
                    <li>MigratePay operates a technology-based platform intended to facilitate transactions between Buyers and Sellers.</li>
                    <li>Unless expressly stated otherwise for a particular Transaction, MigratePay is not the manufacturer, owner, supplier or Seller of the goods or services offered by a Seller.</li>
                    <li>MigratePay's role may include providing technology for transaction initiation, communication, transaction records, payment coordination, dispute management, transaction monitoring and settlement facilitation, subject to Applicable Law.</li>
                    <li>The Buyer and Seller remain responsible for complying with the terms of their underlying transaction.</li>
                    <li>MigratePay does not guarantee the quality, legality, ownership, authenticity, availability, performance or suitability of a Seller's goods or services except to the extent that such responsibility is imposed on MigratePay by Applicable Law or expressly undertaken by MigratePay.</li>
                </ol>
            </section>

            <section id="buyer-responsibilities" class="terms-section">
                <h2>5. Buyer Responsibilities</h2>
                <p>The Buyer agrees to:</p>
                <ol>
                    <li>provide accurate personal and transaction information;</li>
                    <li>review the Seller's offer, description, price, delivery terms and other applicable conditions before committing to a Transaction;</li>
                    <li>pay the agreed Transaction Amount through an authorized payment method;</li>
                    <li>provide truthful information when raising a dispute or refund request;</li>
                    <li>not use the Platform for unlawful, fraudulent or prohibited activities;</li>
                    <li>comply with the applicable terms of the Transaction;</li>
                    <li>promptly notify MigratePay of unauthorized use of the Buyer's Account or payment information.</li>
                </ol>
            </section>

            <section id="seller-responsibilities" class="terms-section">
                <h2>6. Seller Responsibilities</h2>
                <p>The Seller agrees to:</p>
                <ol>
                    <li>provide accurate identity and business information;</li>
                    <li>provide accurate descriptions of goods and services;</li>
                    <li>disclose all material terms, prices, conditions, limitations and delivery requirements;</li>
                    <li>possess all necessary rights, licenses, registrations and permissions required to sell or provide the relevant goods or services;</li>
                    <li>deliver the agreed goods or services within the agreed period;</li>
                    <li>comply with applicable consumer-protection, tax, VAT, licensing and business requirements;</li>
                    <li>not offer prohibited or unlawful goods or services;</li>
                    <li>maintain appropriate records supporting the Transaction;</li>
                    <li>cooperate with MigratePay in investigations, disputes, fraud prevention and compliance reviews.</li>
                </ol>
            </section>

            <section id="formation-transaction" class="terms-section">
                <h2>7. Formation of a Transaction</h2>
                <ol>
                    <li>A Transaction may be initiated when a Buyer and Seller agree on the material terms of the proposed transaction.</li>
                    <li>Such terms may include:
                        <ul>
                            <li>description of goods or services;</li>
                            <li>Transaction Amount;</li>
                            <li>delivery requirements;</li>
                            <li>completion deadline;</li>
                            <li>acceptance criteria;</li>
                            <li>refund conditions;</li>
                            <li>cancellation conditions;</li>
                            <li>applicable Platform Fee; and</li>
                            <li>any additional conditions agreed between the parties.</li>
                        </ul>
                    </li>
                    <li>A Transaction may be confirmed electronically through the Platform.</li>
                    <li>Electronic records, confirmations and communications may be retained by MigratePay as evidence of the Transaction, subject to Applicable Law.</li>
                    <li>A Transaction must not contain any unlawful object or consideration. The Contract Act, 1872 requires enforceable agreements to satisfy requirements including competent parties, free consent, lawful consideration and lawful object.</li>
                </ol>
            </section>

            <section id="payment-amount" class="terms-section">
                <h2>8. Payment and Transaction Amount</h2>
                <ol>
                    <li>The Buyer shall pay the agreed Transaction Amount through a payment method authorized or supported by MigratePay.</li>
                    <li>The Transaction Amount represents the amount agreed between the Buyer and Seller and does not automatically constitute revenue of MigratePay.</li>
                    <li>MigratePay's revenue from a Transaction is the applicable Platform Fee and any other legitimately applicable service charge.</li>
                    <li>MigratePay may use authorized third-party payment service providers, banks or other regulated financial service providers to process payments or settlements.</li>
                    <li>Payment processing may be subject to the terms, limits, verification requirements and processing times of the relevant payment provider.</li>
                </ol>
            </section>

            <section id="platform-fee" class="terms-section">
                <h2>9. Platform Fee</h2>
                <ol>
                    <li>Unless otherwise stated for a particular Transaction, MigratePay's Platform Fee shall be 3% of the applicable Transaction Amount.</li>
                    <li>The applicable fee shall be disclosed to the relevant User before the Transaction is finalized where reasonably practicable.</li>
                    <li>Applicable taxes, VAT, government charges, payment-processing charges or other legally applicable charges may apply separately.</li>
                    <li>MigratePay may change its Platform Fee from time to time. Any revised fee shall apply prospectively unless otherwise permitted by Applicable Law.</li>
                </ol>
            </section>

            <section id="settlement" class="terms-section">
                <h2>10. Settlement to Seller</h2>
                <ol>
                    <li>Subject to Applicable Law, completion of the Transaction conditions and successful verification, MigratePay may arrange Settlement of the Seller's payable amount.</li>
                    <li>The Seller's payable amount may be calculated substantially as: Transaction Amount – applicable Platform Fee – applicable lawful deductions/charges = Seller Settlement Amount.</li>
                    <li>Where the applicable Platform Fee is 3%, the standard calculation would be: Seller Settlement Amount = 97% of the Transaction Amount, before any other applicable lawful deductions or charges.</li>
                    <li>Settlement may be delayed where:
                        <ul>
                            <li>the Transaction is disputed;</li>
                            <li>fraud or suspicious activity is suspected;</li>
                            <li>identity verification is incomplete;</li>
                            <li>payment has not been successfully confirmed;</li>
                            <li>the Seller's settlement information is incorrect;</li>
                            <li>the payment provider or financial institution experiences a delay;</li>
                            <li>a government, court or competent authority issues a lawful instruction; or</li>
                            <li>Applicable Law requires the funds to be withheld or reviewed.</li>
                        </ul>
                    </li>
                </ol>
            </section>

            <section id="payment-regulatory" class="terms-section">
                <h2>11. Important Payment-Regulatory Provision</h2>
                <ol>
                    <li>MigratePay shall conduct payment, collection, holding, transfer or settlement-related activities only to the extent permitted under Applicable Law and within any required licensing, authorization, registration or regulated-partner structure.</li>
                    <li>Nothing in these Terms shall be interpreted as representing that MigratePay is a bank, payment service provider, payment system operator, financial institution, escrow institution or other regulated entity unless MigratePay is legally authorized to make such representation.</li>
                    <li>Where a regulated bank, payment service provider or other authorized entity processes or settles funds on behalf of MigratePay, the relevant provider's applicable terms and regulatory requirements may also apply.</li>
                </ol>
                <div class="clause-important">
                    <strong>Important</strong>
                    <p>This clause is particularly important for MigratePay. Your business model involves receiving the Buyer's money and subsequently settling the Seller's amount. The contractual wording cannot itself create a regulatory authorization that does not otherwise exist.</p>
                </div>
            </section>

            <section id="completion-transaction" class="terms-section">
                <h2>12. Completion of a Transaction</h2>
                <ol>
                    <li>A Transaction shall be considered completed when the applicable completion condition specified in the Transaction has been satisfied.</li>
                    <li>Depending on the nature of the Transaction, completion may be established through:
                        <ul>
                            <li>Buyer confirmation;</li>
                            <li>Seller confirmation;</li>
                            <li>delivery evidence;</li>
                            <li>service completion evidence;</li>
                            <li>agreed acceptance criteria;</li>
                            <li>expiration of a specified review period; or</li>
                            <li>another method agreed through the Platform.</li>
                        </ul>
                    </li>
                    <li>MigratePay may request reasonable evidence where completion is disputed.</li>
                </ol>
            </section>

            <section id="refunds" class="terms-section">
                <h2>13. Refunds</h2>
                <ol>
                    <li>A Buyer may be entitled to a refund where:
                        <ul>
                            <li>the Seller fails to provide the agreed goods or services;</li>
                            <li>the Transaction is cancelled under its applicable terms;</li>
                            <li>the Transaction is found to be fraudulent;</li>
                            <li>the Transaction is unlawful or invalid;</li>
                            <li>a refund is required under Applicable Law; or</li>
                            <li>MigratePay otherwise determines that a refund is appropriate under its published policies.</li>
                        </ul>
                    </li>
                    <li>Refund eligibility may depend on the nature and stage of the Transaction.</li>
                    <li>Where Applicable Law provides a mandatory refund right, that right shall prevail over any conflicting provision of these Terms.</li>
                    <li>Refunds may be processed through the original payment method where reasonably practicable.</li>
                    <li>Payment-provider processing times may affect the time required for a refund to reach the Buyer.</li>
                </ol>
            </section>

            <section id="cancellation" class="terms-section">
                <h2>14. Cancellation</h2>
                <ol>
                    <li>A Transaction may be cancelled before completion where permitted by the applicable Transaction terms.</li>
                    <li>Cancellation after delivery or completion may be subject to additional conditions.</li>
                    <li>A Seller may not cancel a Transaction merely to avoid its obligations under an agreed Transaction.</li>
                    <li>MigratePay may cancel or suspend a Transaction where it reasonably believes that:
                        <ul>
                            <li>the Transaction is fraudulent;</li>
                            <li>the Transaction violates Applicable Law;</li>
                            <li>the Transaction involves prohibited goods or services;</li>
                            <li>the parties have provided materially false information; or</li>
                            <li>continuing the Transaction presents a significant legal, regulatory or security risk.</li>
                        </ul>
                    </li>
                </ol>
            </section>

            <section id="disputes" class="terms-section">
                <h2>15. Disputes</h2>
                <ol>
                    <li>Either party may raise a dispute through MigratePay's designated dispute-resolution mechanism.</li>
                    <li>The complaining party should provide sufficient information and evidence, which may include:
                        <ul>
                            <li>Transaction ID;</li>
                            <li>payment evidence;</li>
                            <li>communications;</li>
                            <li>invoices;</li>
                            <li>delivery evidence;</li>
                            <li>service-completion evidence;</li>
                            <li>photographs or other relevant records.</li>
                        </ul>
                    </li>
                    <li>MigratePay may request additional evidence from either party.</li>
                    <li>While a dispute is under review, MigratePay may delay Settlement where permitted by Applicable Law.</li>
                    <li>MigratePay may consider information available through the Platform when reviewing a dispute.</li>
                    <li>MigratePay's internal dispute process does not remove any statutory right a User may have to seek remedies from a competent authority or court.</li>
                </ol>
            </section>

            <section id="fraud-monitoring" class="terms-section">
                <h2>16. Fraud Prevention and Transaction Monitoring</h2>
                <p>MigratePay may monitor Transactions for indicators of:</p>
                <ul>
                    <li>fraud;</li>
                    <li>identity theft;</li>
                    <li>unauthorized payments;</li>
                    <li>money laundering;</li>
                    <li>terrorist financing;</li>
                    <li>use of stolen funds;</li>
                    <li>suspicious transaction patterns;</li>
                    <li>account abuse;</li>
                    <li>prohibited goods or services; or</li>
                    <li>other unlawful activity.</li>
                </ul>
                <p>Where appropriate, MigratePay may:</p>
                <ul>
                    <li>request additional documentation;</li>
                    <li>delay or suspend Settlement;</li>
                    <li>restrict an Account;</li>
                    <li>cancel a Transaction;</li>
                    <li>refuse a payment;</li>
                    <li>retain records;</li>
                    <li>cooperate with relevant authorities; or</li>
                    <li>take other lawful action.</li>
                </ul>
            </section>

            <section id="aml-source-funds" class="terms-section">
                <h2>17. Anti-Money Laundering and Source of Funds</h2>
                <ol>
                    <li>Users represent that funds used through MigratePay originate from lawful sources.</li>
                    <li>Users must not use MigratePay to conceal the origin, ownership or destination of unlawful funds.</li>
                    <li>MigratePay may conduct identity and transaction verification where required for legal, regulatory, security or fraud-prevention purposes.</li>
                    <li>MigratePay may provide information to competent authorities where required or permitted by Applicable Law.</li>
                </ol>
            </section>

            <section id="prohibited-activities" class="terms-section">
                <h2>18. Prohibited Activities</h2>
                <p>Users must not use MigratePay for:</p>
                <ul>
                    <li>fraud or deception;</li>
                    <li>money laundering;</li>
                    <li>terrorist financing;</li>
                    <li>stolen goods;</li>
                    <li>counterfeit goods;</li>
                    <li>unlawful weapons or prohibited items;</li>
                    <li>illegal drugs;</li>
                    <li>unlawful gambling;</li>
                    <li>trafficking or exploitation;</li>
                    <li>unlawful financial schemes;</li>
                    <li>unauthorized investment or deposit-taking activities;</li>
                    <li>infringement of intellectual property rights;</li>
                    <li>phishing or cybercrime;</li>
                    <li>identity theft;</li>
                    <li>sale or misuse of personal data;</li>
                    <li>unlawful digital content;</li>
                    <li>transactions prohibited by Bangladesh law; or</li>
                    <li>any activity designed to circumvent MigratePay's security, compliance or transaction controls.</li>
                </ul>
            </section>

            <section id="account-registration" class="terms-section">
                <h2>19. Account Registration</h2>
                <ol>
                    <li>Users must provide truthful and current information.</li>
                    <li>Users must not create an Account using another person's identity without lawful authorization.</li>
                    <li>Users are responsible for maintaining the confidentiality of their login credentials.</li>
                    <li>Users must immediately notify MigratePay if they suspect unauthorized access to their Account.</li>
                    <li>MigratePay may require additional verification before allowing access to certain functions.</li>
                </ol>
            </section>

            <section id="kyc-verification" class="terms-section">
                <h2>20. Know Your Customer and Verification</h2>
                <p>MigratePay may request:</p>
                <ul>
                    <li>National Identification information;</li>
                    <li>passport or other identification;</li>
                    <li>business registration information;</li>
                    <li>trade license;</li>
                    <li>Tax Identification Number;</li>
                    <li>bank or payment account information;</li>
                    <li>business address;</li>
                    <li>beneficial ownership information;</li>
                    <li>contact information; and</li>
                    <li>other information reasonably required for verification.</li>
                </ul>
                <p>Failure to provide required verification information may result in restriction, suspension or cancellation of the relevant service or Transaction.</p>
            </section>

            <section id="seller-settlement-account" class="terms-section">
                <h2>21. Seller Settlement Account</h2>
                <ol>
                    <li>Sellers must provide accurate settlement information.</li>
                    <li>A Seller must have the legal right to receive funds into the nominated settlement account.</li>
                    <li>MigratePay may request evidence of ownership or authorization regarding a settlement account.</li>
                    <li>MigratePay is not responsible for delays caused by incorrect settlement information supplied by a Seller, except to the extent caused by MigratePay's own failure to exercise legally required care.</li>
                </ol>
            </section>

            <section id="taxes-vat" class="terms-section">
                <h2>22. Taxes and VAT</h2>
                <ol>
                    <li>Each User is responsible for complying with the tax laws applicable to that User and its business activities.</li>
                    <li>MigratePay shall account for its own taxable income and applicable tax obligations in accordance with Bangladesh law.</li>
                    <li>Where VAT, withholding tax, source tax or another statutory deduction applies to a transaction or service, MigratePay may make the required deduction or adjustment.</li>
                    <li>Users may be required to provide tax-related information or documents where legally required.</li>
                    <li>The Income Tax Act, 2023 is maintained by the National Board of Revenue in an official English version and is part of the tax framework applicable to businesses in Bangladesh.</li>
                </ol>
            </section>

            <section id="consumer-rights" class="terms-section">
                <h2>23. Consumer Rights</h2>
                <p>Nothing in these Terms is intended to exclude or unlawfully restrict any mandatory consumer right available under Bangladesh law.</p>
                <p>Where a mandatory consumer-protection provision applies, that provision shall prevail over any inconsistent term of this Agreement.</p>
                <p>MigratePay may maintain separate consumer complaint, refund and dispute procedures to facilitate the exercise of applicable rights.</p>
            </section>

            <section id="seller-compliance" class="terms-section">
                <h2>24. Seller Compliance</h2>
                <p>Each Seller is solely responsible for obtaining and maintaining any license, registration, approval, certification or authorization required for its business, goods or services.</p>
                <p>The Seller is also responsible for:</p>
                <ul>
                    <li>product/service legality;</li>
                    <li>applicable taxes;</li>
                    <li>VAT obligations;</li>
                    <li>invoices and receipts;</li>
                    <li>warranties;</li>
                    <li>guarantees;</li>
                    <li>delivery;</li>
                    <li>after-sales obligations; and</li>
                    <li>compliance with applicable business laws.</li>
                </ul>
            </section>

            <section id="intellectual-property" class="terms-section">
                <h2>25. Intellectual Property</h2>
                <ol>
                    <li>MigratePay and its licensors retain all rights in the MigratePay name, brand, logo, software, platform design, databases, text, graphics, interface and technology, except where rights belong to third parties.</li>
                    <li>Users may not copy, reproduce, modify, distribute, reverse engineer, sell or commercially exploit MigratePay's proprietary materials without authorization.</li>
                    <li>Users retain ownership of content they lawfully own and submit to the Platform, subject to the rights reasonably required by MigratePay to operate its services.</li>
                </ol>
            </section>

            <section id="user-content" class="terms-section">
                <h2>26. User Content</h2>
                <ol>
                    <li>Users are responsible for information, descriptions, images, documents and other content they upload.</li>
                    <li>Users represent that they have the necessary rights to submit such content.</li>
                    <li>Users must not upload content that is unlawful, defamatory, fraudulent, infringing, malicious or misleading.</li>
                    <li>MigratePay may remove or restrict content that violates these Terms or Applicable Law.</li>
                </ol>
            </section>

            <section id="privacy-data" class="terms-section">
                <h2>27. Privacy and Personal Data</h2>
                <ol>
                    <li>MigratePay may collect and process personal information necessary for: account creation; identity verification; transaction processing; settlement; fraud prevention; customer support; dispute resolution; security; legal compliance; tax and accounting purposes; and other legitimate business purposes.</li>
                    <li>MigratePay shall process personal data in accordance with its Privacy Policy and Applicable Law.</li>
                    <li>Users may be required to provide consent where consent is required by Applicable Law.</li>
                    <li>MigratePay may disclose information where required by law, court order, regulator, law-enforcement authority or other competent authority.</li>
                </ol>
            </section>

            <section id="electronic-communications" class="terms-section">
                <h2>28. Electronic Communications</h2>
                <p>By using MigratePay, you consent to receiving electronic communications relating to your Account and Transactions, including: transaction confirmations; payment notifications; settlement notifications; refund notifications; security alerts; account notices; policy updates; and service-related communications.</p>
                <p>Electronic records and communications may constitute evidence of Transactions to the extent permitted by Applicable Law.</p>
            </section>

            <section id="transaction-records" class="terms-section">
                <h2>29. Transaction Records</h2>
                <p>MigratePay may retain records relating to Transactions, including: User information; Transaction details; payment information; communications; confirmations; dispute records; refund records; Settlement records; and security or verification records.</p>
                <p>Records may be retained for legal, regulatory, tax, accounting, security and dispute-resolution purposes.</p>
            </section>

            <section id="third-party-providers" class="terms-section">
                <h2>30. Third-Party Service Providers</h2>
                <p>MigratePay may use third-party providers for: payment processing; banking; identity verification; communication; hosting; cloud infrastructure; fraud prevention; analytics; customer support; and other operational functions.</p>
                <p>Where appropriate, Users may also be subject to the relevant third party's terms and policies.</p>
            </section>

            <section id="third-party-websites" class="terms-section">
                <h2>31. Third-Party Websites</h2>
                <p>MigratePay may contain links or integrations to third-party websites or services.</p>
                <p>MigratePay does not control third-party websites and is not responsible for their content, policies, availability or security except where required by Applicable Law.</p>
            </section>

            <section id="service-availability" class="terms-section">
                <h2>32. Service Availability</h2>
                <p>MigratePay will make reasonable efforts to keep the Platform operational.</p>
                <p>However, uninterrupted availability is not guaranteed.</p>
                <p>The Platform may become temporarily unavailable because of: maintenance; software updates; infrastructure failures; telecommunications problems; banking or payment-system outages; cyber incidents; government restrictions; natural disasters; force majeure events; or other circumstances outside MigratePay's reasonable control.</p>
            </section>

            <section id="security" class="terms-section">
                <h2>33. Security</h2>
                <p>MigratePay will take reasonable technical and organizational measures designed to protect its systems and User information.</p>
                <p>However, no online system can be guaranteed to be completely secure.</p>
                <p>Users must also take reasonable steps to protect their Accounts and authentication credentials.</p>
            </section>

            <section id="account-suspension" class="terms-section">
                <h2>34. Account Suspension and Termination</h2>
                <p>MigratePay may suspend, restrict or terminate an Account where reasonably necessary because of: violation of these Terms; fraud; suspected unlawful activity; inaccurate information; security risks; suspicious Transactions; failure to complete required verification; regulatory requirements; court or government orders; or other legitimate compliance or security reasons.</p>
                <p>Where legally permissible, MigratePay may notify the affected User of the reason.</p>
            </section>

            <section id="consequences-termination" class="terms-section">
                <h2>35. Consequences of Termination</h2>
                <p>Termination of an Account does not automatically extinguish obligations or liabilities arising before termination.</p>
                <p>Outstanding legitimate Transactions, refunds, disputes, fees, tax obligations and other matters may continue to be processed after Account termination.</p>
            </section>

            <section id="indemnification" class="terms-section">
                <h2>36. Indemnification</h2>
                <p>To the extent permitted by Applicable Law, a User shall indemnify and hold MigratePay, its partners, officers, employees, contractors and service providers harmless from claims, losses, liabilities, damages, costs and reasonable legal expenses arising from: the User's breach of these Terms; fraud; unlawful conduct; infringement of third-party rights; misuse of the Platform; false information; or breach of the User's underlying Transaction obligations.</p>
            </section>

            <section id="limitation-liability" class="terms-section">
                <h2>37. Limitation of Liability</h2>
                <p>To the maximum extent permitted by Applicable Law, MigratePay shall not be liable for losses arising solely from: a Seller's failure to perform; a Buyer's failure to perform; inaccurate information provided by a User; unauthorized conduct by another User; third-party service failures; payment-provider delays; events outside MigratePay's reasonable control; or unlawful conduct by a User.</p>
                <p>Nothing in these Terms excludes or limits liability that cannot lawfully be excluded or limited under Bangladesh law.</p>
            </section>

            <section id="no-unlawful-exclusion" class="terms-section">
                <h2>38. No Unlawful Exclusion of Statutory Rights</h2>
                <p>Nothing in these Terms shall: exclude mandatory statutory rights; prevent a User from exercising rights granted by Applicable Law; remove the jurisdiction of a competent court where such jurisdiction is legally available; override a mandatory regulatory requirement; or restrict any remedy that cannot legally be restricted.</p>
            </section>

            <section id="force-majeure" class="terms-section">
                <h2>39. Force Majeure</h2>
                <p>MigratePay shall not be responsible for delay or failure caused by circumstances beyond its reasonable control, including natural disasters, war, civil unrest, epidemics, government actions, infrastructure failures, widespread telecommunications outages, banking-system failures, cyber incidents or similar events.</p>
            </section>

            <section id="notices" class="terms-section">
                <h2>40. Notices</h2>
                <p>MigratePay may provide notices through: registered email; registered mobile number; Platform notifications; website notices; or other legally permissible electronic methods.</p>
                <p>Users are responsible for maintaining accurate contact information.</p>
            </section>

            <section id="changes-terms" class="terms-section">
                <h2>41. Changes to These Terms</h2>
                <p>MigratePay may amend these Terms from time to time.</p>
                <p>Material amendments may be communicated through appropriate channels where required by Applicable Law.</p>
                <p>The revised Terms shall become effective on the stated effective date.</p>
                <p>Continued use of the Platform after the effective date may constitute acceptance of the revised Terms to the extent permitted by Applicable Law.</p>
            </section>

            <section id="severability" class="terms-section">
                <h2>42. Severability</h2>
                <p>If any provision of these Terms is determined to be invalid, unlawful or unenforceable, that provision shall be interpreted or modified to the minimum extent necessary to make it enforceable where legally possible.</p>
                <p>The remaining provisions shall continue in effect.</p>
            </section>

            <section id="waiver" class="terms-section">
                <h2>43. Waiver</h2>
                <p>Failure by MigratePay to enforce any provision of these Terms shall not constitute a waiver of its right to enforce that provision later.</p>
            </section>

            <section id="assignment" class="terms-section">
                <h2>44. Assignment</h2>
                <p>Users may not assign their rights or obligations under these Terms without MigratePay's prior written consent, except where otherwise permitted by Applicable Law.</p>
                <p>MigratePay may assign or transfer its rights and obligations as part of a lawful restructuring, merger, acquisition, sale of business or similar transaction.</p>
            </section>

            <section id="relationship" class="terms-section">
                <h2>45. Relationship Between the Parties</h2>
                <p>Nothing in these Terms creates a partnership, joint venture, employment relationship, agency relationship or fiduciary relationship between MigratePay and a User unless expressly established through a separate written agreement.</p>
            </section>

            <section id="entire-agreement" class="terms-section">
                <h2>46. Entire Agreement</h2>
                <p>These Terms, together with the Privacy Policy, Refund Policy, Transaction Policy, Prohibited Activities Policy and any specific Transaction terms, constitute the applicable contractual framework between MigratePay and the User regarding use of the Platform.</p>
                <p>Where a specific written Transaction term conflicts with a general Platform term, the specific Transaction term may apply to that Transaction, subject always to Applicable Law.</p>
            </section>

            <section id="governing-law" class="terms-section">
                <h2>47. Governing Law</h2>
                <p>These Terms shall be governed by and interpreted in accordance with the laws of Bangladesh.</p>
                <p>Any matter not expressly addressed in these Terms shall be determined in accordance with Applicable Law.</p>
            </section>

            <section id="dispute-resolution" class="terms-section">
                <h2>48. Dispute Resolution</h2>
                <p>The parties should first attempt to resolve disputes through good-faith communication and MigratePay's internal complaint mechanism.</p>
                <p>If a dispute cannot be resolved internally, either party may pursue remedies available under Applicable Law before an appropriate court, tribunal, regulator, authority or other lawful dispute-resolution forum.</p>
                <p>Nothing in this clause prevents a User from exercising a mandatory statutory right.</p>
            </section>

            <section id="contact-information" class="terms-section">
                <h2>49. Contact Information</h2>
                <p>MigratePay</p>
                <ul>
                    <li><strong>Legal Entity Name:</strong> [Full Legal Name]</li>
                    <li><strong>Business Address:</strong> [Full Address]</li>
                    <li><strong>Email:</strong> [Official Email]</li>
                    <li><strong>Support Email:</strong> [Support Email]</li>
                    <li><strong>Legal/Compliance Email:</strong> [Legal Email]</li>
                    <li><strong>Phone:</strong> [Phone Number]</li>
                    <li><strong>Website:</strong> [Website URL]</li>
                </ul>
            </section>

            <section id="user-acknowledgement" class="terms-section">
                <h2>50. User Acknowledgement</h2>
                <p>By registering for, accessing or using MigratePay, you acknowledge and agree that:</p>
                <ul>
                    <li>you have read and understood these Terms;</li>
                    <li>the information you provide is accurate;</li>
                    <li>you have authority to enter into Transactions you undertake;</li>
                    <li>you will comply with Bangladesh law;</li>
                    <li>you will not use MigratePay for unlawful activities;</li>
                    <li>you will honor your Transaction obligations;</li>
                    <li>you will pay applicable Platform Fees and lawful charges;</li>
                    <li>you will cooperate with reasonable verification and compliance procedures; and</li>
                    <li>you accept the applicable MigratePay policies governing your use of the Platform.</li>
                </ul>
            </section>
            
            <div class="terms-footer-cta">
                <h2>Have questions about these terms?</h2>
                <p>Contact our team if you need clarification regarding our Terms & Conditions.</p>
                <a href="/contact" class="btn-contact">Contact Us &rarr;</a>
            </div>
            
        </div>
    </div>
</div>

<!-- Back to top button -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.terms-section');
    const navLinks = document.querySelectorAll('.toc-link');
    const backToTop = document.getElementById('backToTop');
    const mobileToggle = document.getElementById('mobileTocToggle');
    const sidebar = document.getElementById('termsSidebar');
    const tocChevron = document.getElementById('tocChevron');

    // 1. Intersection Observer for ScrollSpy
    const observerOptions = {
        root: null,
        rootMargin: '-120px 0px -60% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('active');
                        
                        // Keep active link visible in the sidebar
                        const linkTop = link.offsetTop;
                        const sidebarScrollTop = sidebar.scrollTop;
                        const sidebarHeight = sidebar.clientHeight;
                        
                        if (linkTop < sidebarScrollTop || linkTop > sidebarScrollTop + sidebarHeight - 50) {
                            sidebar.scrollTo({
                                top: linkTop - 100,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        observer.observe(section);
    });

    // 2. Smooth Scrolling for TOC Links
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                // Close mobile menu if open
                if (sidebar.classList.contains('expanded')) {
                    sidebar.classList.remove('expanded');
                    tocChevron.style.transform = 'rotate(0deg)';
                }

                // Smooth scroll
                window.scrollTo({
                    top: targetSection.offsetTop - 100, // Account for fixed header/margins
                    behavior: 'smooth'
                });
                
                // Update URL hash without jumping
                history.pushState(null, null, targetId);
            }
        });
    });

    // 3. Back to Top Button
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // 4. Mobile TOC Toggle
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        if (sidebar.classList.contains('expanded')) {
            tocChevron.style.transform = 'rotate(180deg)';
        } else {
            tocChevron.style.transform = 'rotate(0deg)';
        }
    });

    // 5. Handle initial URL hash
    if (window.location.hash) {
        setTimeout(() => {
            const targetSection = document.querySelector(window.location.hash);
            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        }, 100);
    }
});
</script>
@endsection
