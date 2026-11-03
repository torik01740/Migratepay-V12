@extends('layouts.frontend')
@section('title', 'Privacy Policy | Migratepay')

@section('content')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
<script src="{{ asset('js/legal-pages.js') }}"></script>

<div class="terms-page">
    <div class="terms-hero">
        <h1>PRIVACY POLICY</h1>
        <p>Migratepay Digital Solution respects your privacy and is committed to protecting the personal information of individuals who access or use our website, platform, applications, products and services.</p>
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
                <li><a href="#about" class="toc-link">1. About Migratepay</a></li>
                <li><a href="#scope" class="toc-link">2. Scope of This Privacy Policy</a></li>
                <li><a href="#info-collect" class="toc-link">3. Information We Collect</a></li>
                <li><a href="#identity-verification" class="toc-link">4. Identity and Verification Information</a></li>
                <li><a href="#buyer-seller-info" class="toc-link">5. Buyer and Seller Information</a></li>
                <li><a href="#transaction-info" class="toc-link">6. Transaction Information</a></li>
                <li><a href="#payment-info" class="toc-link">7. Payment Information</a></li>
                <li><a href="#bank-settlement" class="toc-link">8. Bank and Settlement Information</a></li>
                <li><a href="#communications-support" class="toc-link">9. Communications and Support Information</a></li>
                <li><a href="#device-technical" class="toc-link">10. Device and Technical Information</a></li>
                <li><a href="#cookies" class="toc-link">11. Cookies and Similar Technologies</a></li>
                <li><a href="#how-we-use" class="toc-link">12. How Migratepay Uses Personal Information</a></li>
                <li><a href="#legal-basis" class="toc-link">13. Legal Basis for Processing</a></li>
                <li><a href="#when-we-share" class="toc-link">14. When Migratepay Shares Information</a></li>
                <li><a href="#shared-between-users" class="toc-link">15. Information Shared Between Buyers and Sellers</a></li>
                <li><a href="#data-minimization" class="toc-link">16. Data Minimization</a></li>
                <li><a href="#accuracy" class="toc-link">17. Accuracy of Information</a></li>
                <li><a href="#data-security" class="toc-link">18. Data Security</a></li>
                <li><a href="#user-account-security" class="toc-link">19. User Account Security</a></li>
                <li><a href="#data-retention" class="toc-link">20. Data Retention</a></li>
                <li><a href="#account-closure" class="toc-link">21. Account Closure and Deletion</a></li>
                <li><a href="#privacy-rights" class="toc-link">22. Privacy Rights</a></li>
                <li><a href="#withdrawal-consent" class="toc-link">23. Withdrawal of Consent</a></li>
                <li><a href="#marketing-communications" class="toc-link">24. Marketing Communications</a></li>
                <li><a href="#third-party-providers" class="toc-link">25. Third-Party Service Providers</a></li>
                <li><a href="#international-transfers" class="toc-link">26. International Data Transfers</a></li>
                <li><a href="#childrens-privacy" class="toc-link">27. Children's Privacy</a></li>
                <li><a href="#sensitive-info" class="toc-link">28. Sensitive Personal Information</a></li>
                <li><a href="#analytics-usage" class="toc-link">29. Analytics and Website Usage</a></li>
                <li><a href="#log-files" class="toc-link">30. Log Files</a></li>
                <li><a href="#fraud-investigations" class="toc-link">31. Fraud and Security Investigations</a></li>
                <li><a href="#legal-disclosure" class="toc-link">32. Legal Disclosure</a></li>
                <li><a href="#business-transfers" class="toc-link">33. Business Transfers</a></li>
                <li><a href="#third-party-websites" class="toc-link">34. Third-Party Websites</a></li>
                <li><a href="#no-absolute-security" class="toc-link">35. No Absolute Security Guarantee</a></li>
                <li><a href="#data-breaches" class="toc-link">36. Data Breaches and Security Incidents</a></li>
                <li><a href="#compliance-bd-law" class="toc-link">37. Compliance With Bangladesh Law</a></li>
                <li><a href="#changes-privacy-policy" class="toc-link">38. Changes to This Privacy Policy</a></li>
                <li><a href="#privacy-requests" class="toc-link">39. Privacy Requests and Contact</a></li>
                <li><a href="#privacy-complaints" class="toc-link">40. Privacy Complaints</a></li>
                <li><a href="#governing-law" class="toc-link">41. Governing Law</a></li>
                <li><a href="#acceptance" class="toc-link">42. Acceptance</a></li>
                <li><a href="#summary" class="toc-link">Privacy Policy Summary</a></li>
            </ul>
        </aside>

        <!-- Right Content -->
        <div class="terms-content">
            <section class="terms-section" style="margin-top:-30px;">
                <p>This Privacy Policy explains how Migratepay Digital Solution collects, uses, processes, stores, shares, protects and otherwise handles personal information and other information relating to users of Migratepay.</p>
                <p>By accessing or using the Migratepay platform, creating an account, initiating or participating in a transaction, submitting information to us, or otherwise interacting with our services, you acknowledge that you have read and understood this Privacy Policy.</p>
                <p>Where applicable law requires your consent for a particular processing activity, we will obtain such consent in the manner required by law.</p>
            </section>

            <section id="about" class="terms-section">
                <h2>1. About Migratepay</h2>
                <p>Migratepay Digital Solution operates Migratepay, a technology-based platform designed to facilitate transactions between Buyers and Sellers.</p>
                <p>Migratepay may provide technology and related services for:</p>
                <ul>
                    <li>Buyer and Seller registration;</li>
                    <li>transaction creation;</li>
                    <li>transaction management;</li>
                    <li>payment coordination;</li>
                    <li>transaction verification;</li>
                    <li>settlement facilitation;</li>
                    <li>refunds;</li>
                    <li>dispute management;</li>
                    <li>fraud prevention;</li>
                    <li>customer support; and</li>
                    <li>related administrative and compliance functions.</li>
                </ul>
                <p>Depending on the specific service and transaction structure, Migratepay may work with banks, payment service providers, payment processors, identity-verification providers and other third-party service providers.</p>
            </section>

            <section id="scope" class="terms-section">
                <h2>2. Scope of This Privacy Policy</h2>
                <p>This Privacy Policy applies to information collected through or in connection with:</p>
                <ul>
                    <li>the Migratepay website;</li>
                    <li>Migratepay accounts;</li>
                    <li>Migratepay applications;</li>
                    <li>Migratepay software and technology;</li>
                    <li>Buyer and Seller services;</li>
                    <li>transactions conducted through Migratepay;</li>
                    <li>payment and settlement-related processes;</li>
                    <li>customer support;</li>
                    <li>dispute-resolution processes;</li>
                    <li>identity and business verification;</li>
                    <li>communications with Migratepay;</li>
                    <li>promotional activities; and</li>
                    <li>other services operated by Migratepay Digital Solution.</li>
                </ul>
                <p>This Privacy Policy does not govern the privacy practices of independent third-party websites, banks, payment providers or other external services.</p>
                <p>Those third parties may maintain their own privacy policies and terms.</p>
            </section>

            <section id="info-collect" class="terms-section">
                <h2>3. Information We Collect</h2>
                <p>The information we collect depends on how you use Migratepay.</p>
                <p>We may collect the following categories of information.</p>
                
                <h3>3.1 Account Information</h3>
                <p>When you create or maintain an account, we may collect:</p>
                <ul>
                    <li>full name;</li>
                    <li>username;</li>
                    <li>email address;</li>
                    <li>mobile telephone number;</li>
                    <li>password or authentication information;</li>
                    <li>profile information;</li>
                    <li>account type;</li>
                    <li>address;</li>
                    <li>country;</li>
                    <li>communication preferences;</li>
                    <li>account status; and</li>
                    <li>other information necessary to create and maintain your account.</li>
                </ul>
            </section>

            <section id="identity-verification" class="terms-section">
                <h2>4. Identity and Verification Information</h2>
                <p>To protect Migratepay, its Users and Transactions, we may request identity or business verification information.</p>
                <p>Depending on the circumstances, this may include:</p>
                <ul>
                    <li>National Identification information;</li>
                    <li>passport information;</li>
                    <li>date of birth;</li>
                    <li>photograph;</li>
                    <li>signature;</li>
                    <li>residential or business address;</li>
                    <li>trade license;</li>
                    <li>Tax Identification Number;</li>
                    <li>business registration information;</li>
                    <li>partnership or company information;</li>
                    <li>beneficial ownership information;</li>
                    <li>authorized representative information;</li>
                    <li>bank or settlement-account information; and</li>
                    <li>other documents reasonably required for verification.</li>
                </ul>
                <p>We may collect and process this information for purposes including identity verification, transaction security, fraud prevention, compliance, legal obligations and legitimate business purposes.</p>
            </section>

            <section id="buyer-seller-info" class="terms-section">
                <h2>5. Buyer and Seller Information</h2>
                <p>Because Migratepay facilitates transactions between Buyers and Sellers, we may collect information relating to both parties.</p>
                <p>This may include:</p>
                <ul>
                    <li>name;</li>
                    <li>contact information;</li>
                    <li>account information;</li>
                    <li>business information;</li>
                    <li>transaction history;</li>
                    <li>transaction preferences;</li>
                    <li>delivery information;</li>
                    <li>payment-related information;</li>
                    <li>settlement information;</li>
                    <li>communications;</li>
                    <li>dispute information; and</li>
                    <li>other information reasonably necessary to administer a transaction.</li>
                </ul>
            </section>

            <section id="transaction-info" class="terms-section">
                <h2>6. Transaction Information</h2>
                <p>When you initiate, participate in or complete a transaction through Migratepay, we may collect:</p>
                <ul>
                    <li>Transaction ID;</li>
                    <li>Buyer information;</li>
                    <li>Seller information;</li>
                    <li>Transaction Amount;</li>
                    <li>Platform Fee;</li>
                    <li>transaction date and time;</li>
                    <li>goods or services information;</li>
                    <li>transaction description;</li>
                    <li>agreed terms;</li>
                    <li>delivery information;</li>
                    <li>completion information;</li>
                    <li>cancellation information;</li>
                    <li>refund information;</li>
                    <li>dispute information;</li>
                    <li>settlement information;</li>
                    <li>transaction status;</li>
                    <li>supporting documents; and</li>
                    <li>communications relating to the transaction.</li>
                </ul>
                <p>Migratepay may retain transaction records for legal, accounting, tax, security, regulatory and dispute-resolution purposes.</p>
            </section>

            <section id="payment-info" class="terms-section">
                <h2>7. Payment Information</h2>
                <p>Migratepay may receive or process information relating to payments made through the Platform.</p>
                <p>Depending on the payment method, this may include:</p>
                <ul>
                    <li>payment amount;</li>
                    <li>payment status;</li>
                    <li>payment reference;</li>
                    <li>transaction identifier;</li>
                    <li>payment method;</li>
                    <li>payer information;</li>
                    <li>recipient information;</li>
                    <li>payment-provider information;</li>
                    <li>settlement information;</li>
                    <li>refund information; and</li>
                    <li>other payment-related records.</li>
                </ul>

                <h3>7.1 Payment Card Information</h3>
                <p>Where a third-party payment processor handles payment-card transactions, Migratepay may not directly store complete card numbers, CVV/CVC codes or similar authentication information.</p>
                <p>Such information may be collected and processed directly by the relevant payment provider under its own privacy and security policies.</p>
                <p>Migratepay will not normally ask you to provide your password, PIN, CVV/CVC, one-time password or other confidential authentication credentials through email, telephone, social media or ordinary customer-support communications.</p>
            </section>

            <section id="bank-settlement" class="terms-section">
                <h2>8. Bank and Settlement Information</h2>
                <p>Where applicable, Sellers may provide information necessary to receive settlements.</p>
                <p>This may include:</p>
                <ul>
                    <li>bank name;</li>
                    <li>account name;</li>
                    <li>account number;</li>
                    <li>branch information;</li>
                    <li>routing information;</li>
                    <li>mobile financial service information;</li>
                    <li>payment-provider account information;</li>
                    <li>settlement reference information; and</li>
                    <li>other information required to process or verify a lawful settlement.</li>
                </ul>
                <p>Migratepay may use this information to facilitate, verify, reconcile and record settlements.</p>
            </section>

            <section id="communications-support" class="terms-section">
                <h2>9. Communications and Support Information</h2>
                <p>When you contact Migratepay, we may collect information contained in your communications.</p>
                <p>This may include:</p>
                <ul>
                    <li>emails;</li>
                    <li>support tickets;</li>
                    <li>chat messages;</li>
                    <li>complaints;</li>
                    <li>dispute submissions;</li>
                    <li>documents;</li>
                    <li>attachments;</li>
                    <li>transaction-related communications; and</li>
                    <li>other correspondence.</li>
                </ul>
                <p>We may use this information to:</p>
                <ul>
                    <li>provide customer support;</li>
                    <li>investigate complaints;</li>
                    <li>resolve disputes;</li>
                    <li>investigate fraud;</li>
                    <li>improve our services;</li>
                    <li>maintain records; and</li>
                    <li>comply with legal or regulatory requirements.</li>
                </ul>
            </section>

            <section id="device-technical" class="terms-section">
                <h2>10. Device and Technical Information</h2>
                <p>When you access Migratepay, certain information may automatically be collected.</p>
                <p>This may include:</p>
                <ul>
                    <li>IP address;</li>
                    <li>browser type;</li>
                    <li>operating system;</li>
                    <li>device type;</li>
                    <li>device identifiers;</li>
                    <li>language settings;</li>
                    <li>time zone;</li>
                    <li>referring website;</li>
                    <li>pages visited;</li>
                    <li>access times;</li>
                    <li>session information;</li>
                    <li>error logs;</li>
                    <li>security logs; and</li>
                    <li>other technical information.</li>
                </ul>
                <p>This information may be used for security, fraud prevention, troubleshooting, analytics and service improvement.</p>
            </section>

            <section id="cookies" class="terms-section">
                <h2>11. Cookies and Similar Technologies</h2>
                <p>Migratepay may use cookies, local storage, pixels, session technologies and similar technologies.</p>
                <p>These technologies may be used to:</p>
                <ul>
                    <li>keep users signed in;</li>
                    <li>maintain account security;</li>
                    <li>remember preferences;</li>
                    <li>maintain transaction sessions;</li>
                    <li>analyze website performance;</li>
                    <li>detect suspicious activity;</li>
                    <li>improve the user experience;</li>
                    <li>understand website usage; and</li>
                    <li>support marketing activities where permitted by law.</li>
                </ul>
                <p>You may be able to control cookies through your browser settings.</p>
                <p>Disabling certain cookies may affect some Migratepay functionality.</p>
            </section>

            <section id="how-we-use" class="terms-section">
                <h2>12. How Migratepay Uses Personal Information</h2>
                <p>Migratepay may use personal information for lawful and legitimate purposes, including:</p>

                <h3>12.1 Providing Our Services</h3>
                <ul>
                    <li>creating and managing accounts;</li>
                    <li>authenticating Users;</li>
                    <li>facilitating transactions;</li>
                    <li>processing transaction information;</li>
                    <li>coordinating payment-related activities;</li>
                    <li>facilitating settlement;</li>
                    <li>processing refunds;</li>
                    <li>handling cancellations;</li>
                    <li>providing customer support.</li>
                </ul>

                <h3>12.2 Security and Fraud Prevention</h3>
                <ul>
                    <li>detecting suspicious activity;</li>
                    <li>preventing fraud;</li>
                    <li>preventing unauthorized transactions;</li>
                    <li>protecting accounts;</li>
                    <li>investigating security incidents;</li>
                    <li>enforcing Platform policies.</li>
                </ul>

                <h3>12.3 Dispute Resolution</h3>
                <ul>
                    <li>investigating complaints;</li>
                    <li>reviewing transaction evidence;</li>
                    <li>communicating with Buyers and Sellers;</li>
                    <li>reviewing refund requests;</li>
                    <li>determining appropriate action;</li>
                    <li>maintaining dispute records.</li>
                </ul>

                <h3>12.4 Legal and Regulatory Compliance</h3>
                <p>Migratepay may process information where reasonably necessary to:</p>
                <ul>
                    <li>comply with applicable laws;</li>
                    <li>respond to lawful government requests;</li>
                    <li>comply with court orders;</li>
                    <li>respond to regulators;</li>
                    <li>satisfy tax obligations;</li>
                    <li>prevent unlawful activity;</li>
                    <li>establish or defend legal claims.</li>
                </ul>

                <h3>12.5 Business Operations</h3>
                <p>We may also use information to:</p>
                <ul>
                    <li>maintain our Platform;</li>
                    <li>maintain accounting records;</li>
                    <li>analyze performance;</li>
                    <li>improve our services;</li>
                    <li>develop new services;</li>
                    <li>troubleshoot technical problems;</li>
                    <li>maintain security;</li>
                    <li>conduct audits;</li>
                    <li>communicate important service information.</li>
                </ul>
            </section>

            <section id="legal-basis" class="terms-section">
                <h2>13. Legal Basis for Processing</h2>
                <p>Depending on the circumstances and applicable law, Migratepay may process personal information on the basis of:</p>
                <ul>
                    <li>your consent;</li>
                    <li>performance of a contract;</li>
                    <li>taking steps at your request before entering into a contract;</li>
                    <li>compliance with a legal obligation;</li>
                    <li>protection of legitimate interests;</li>
                    <li>prevention or detection of fraud or unlawful activity;</li>
                    <li>protection of the security and integrity of the Platform; or</li>
                    <li>another lawful basis recognized by Applicable Law.</li>
                </ul>
                <p>Where consent is required, Migratepay will seek consent through an appropriate mechanism.</p>
            </section>

            <section id="when-we-share" class="terms-section">
                <h2>14. When Migratepay Shares Information</h2>
                <p>Migratepay does not intend to sell personal information simply as a commercial commodity.</p>
                <p>However, we may disclose or provide access to personal information where reasonably necessary for operating Migratepay, fulfilling transactions, protecting Users, preventing fraud, complying with law or performing other legitimate functions.</p>
                <p>Information may be shared with:</p>
                <ul>
                    <li><strong>Payment Providers:</strong> To process or facilitate payments, refunds or settlements.</li>
                    <li><strong>Banks and Financial Institutions:</strong> Where necessary for payment or settlement processing.</li>
                    <li><strong>Identity and Verification Providers:</strong> To verify identity, business information or other required information.</li>
                    <li><strong>Technology Providers:</strong> Including hosting, cloud infrastructure, software, security and communication providers.</li>
                    <li><strong>Fraud and Security Providers:</strong> To detect, investigate or prevent fraudulent or unauthorized activity.</li>
                    <li><strong>Professional Advisers:</strong> Including lawyers, accountants, auditors and other professional advisers where reasonably necessary.</li>
                    <li><strong>Government and Regulatory Authorities:</strong> Where disclosure is required or permitted by Applicable Law.</li>
                    <li><strong>Courts and Law-Enforcement Authorities:</strong> Where required in response to lawful proceedings, orders or requests.</li>
                    <li><strong>Business Transaction Parties:</strong> Where necessary for mergers, acquisitions, restructuring, financing, sale of assets or similar legitimate business transactions.</li>
                </ul>
            </section>

            <section id="shared-between-users" class="terms-section">
                <h2>15. Information Shared Between Buyers and Sellers</h2>
                <p>Because Migratepay facilitates transactions between Users, certain information may need to be made available to the other party.</p>
                <p>Depending on the nature of the transaction, this may include:</p>
                <ul>
                    <li>name;</li>
                    <li>relevant contact information;</li>
                    <li>transaction details;</li>
                    <li>delivery information;</li>
                    <li>transaction status;</li>
                    <li>relevant communications;</li>
                    <li>dispute-related information; and</li>
                    <li>other information reasonably necessary to complete or administer the transaction.</li>
                </ul>
                <p>Migratepay will seek to limit such disclosure to information reasonably necessary for the relevant purpose.</p>
            </section>

            <section id="data-minimization" class="terms-section">
                <h2>16. Data Minimization</h2>
                <p>Migratepay seeks to collect information reasonably necessary for the relevant purpose.</p>
                <p>However, certain services may require additional information for:</p>
                <ul>
                    <li>identity verification;</li>
                    <li>transaction security;</li>
                    <li>fraud prevention;</li>
                    <li>payment processing;</li>
                    <li>settlement;</li>
                    <li>legal compliance;</li>
                    <li>tax compliance; or</li>
                    <li>regulatory requirements.</li>
                </ul>
                <p>If required information is not provided, Migratepay may be unable to provide certain services or complete certain transactions.</p>
            </section>

            <section id="accuracy" class="terms-section">
                <h2>17. Accuracy of Information</h2>
                <p>Users are responsible for ensuring that information submitted to Migratepay is accurate, complete and current.</p>
                <p>You should promptly update information when it changes.</p>
                <p>Migratepay may request additional information where reasonably necessary to verify or update existing information.</p>
            </section>

            <section id="data-security" class="terms-section">
                <h2>18. Data Security</h2>
                <p>Migratepay Digital Solution takes reasonable technical and organizational measures designed to protect personal information against:</p>
                <ul>
                    <li>unauthorized access;</li>
                    <li>unauthorized disclosure;</li>
                    <li>alteration;</li>
                    <li>loss;</li>
                    <li>destruction;</li>
                    <li>misuse;</li>
                    <li>fraudulent activity; and</li>
                    <li>other reasonably foreseeable security risks.</li>
                </ul>
                <p>These measures may include:</p>
                <ul>
                    <li>access controls;</li>
                    <li>authentication systems;</li>
                    <li>encryption where appropriate;</li>
                    <li>security logging;</li>
                    <li>monitoring;</li>
                    <li>backups;</li>
                    <li>system security controls;</li>
                    <li>employee access restrictions;</li>
                    <li>internal security procedures; and</li>
                    <li>third-party security controls.</li>
                </ul>
                <p>However, no Internet transmission, electronic storage system or database can be guaranteed to be completely secure.</p>
                <p>Accordingly, Migratepay cannot guarantee absolute security of information.</p>
            </section>

            <section id="user-account-security" class="terms-section">
                <h2>19. User Account Security</h2>
                <p>Users are responsible for maintaining the confidentiality of:</p>
                <ul>
                    <li>passwords;</li>
                    <li>authentication credentials;</li>
                    <li>security codes;</li>
                    <li>recovery information; and</li>
                    <li>other Account credentials.</li>
                </ul>
                <p>Users must immediately notify Migratepay if they suspect unauthorized access to their Account.</p>
                <p>Migratepay will not normally request your password or one-time authentication code through ordinary support communications.</p>
            </section>

            <section id="data-retention" class="terms-section">
                <h2>20. Data Retention</h2>
                <p>Migratepay retains personal information only for as long as reasonably necessary for the purposes described in this Privacy Policy, unless a longer period is required or permitted by Applicable Law.</p>
                <p>Retention periods may depend on:</p>
                <ul>
                    <li>the nature of the information;</li>
                    <li>the purpose of processing;</li>
                    <li>ongoing transactions;</li>
                    <li>legal requirements;</li>
                    <li>tax requirements;</li>
                    <li>accounting requirements;</li>
                    <li>dispute periods;</li>
                    <li>fraud-prevention requirements;</li>
                    <li>regulatory requirements;</li>
                    <li>security requirements; and</li>
                    <li>legitimate business requirements.</li>
                </ul>
                <p>Certain transaction, payment, accounting, tax, compliance and dispute records may therefore be retained after an Account has been closed.</p>
            </section>

            <section id="account-closure" class="terms-section">
                <h2>21. Account Closure and Deletion</h2>
                <p>You may request closure of your Migratepay Account.</p>
                <p>Where legally available, you may also request deletion of certain personal information.</p>
                <p>However, closing an Account does not necessarily result in immediate deletion of all information.</p>
                <p>Migratepay may retain information where necessary or permitted for:</p>
                <ul>
                    <li>legal compliance;</li>
                    <li>regulatory compliance;</li>
                    <li>tax and accounting obligations;</li>
                    <li>fraud prevention;</li>
                    <li>security;</li>
                    <li>dispute resolution;</li>
                    <li>establishment or defense of legal claims;</li>
                    <li>transaction records; or</li>
                    <li>other lawful purposes.</li>
                </ul>
            </section>

            <section id="privacy-rights" class="terms-section">
                <h2>22. Privacy Rights</h2>
                <p>Subject to Applicable Law and any applicable conditions or limitations, Users may have rights relating to their personal information, including the right to:</p>
                <ul>
                    <li>know whether their information is being processed;</li>
                    <li>obtain information regarding processing;</li>
                    <li>access certain personal information;</li>
                    <li>request correction of inaccurate information;</li>
                    <li>request deletion where legally available;</li>
                    <li>withdraw consent where consent is the applicable legal basis;</li>
                    <li>object to certain processing;</li>
                    <li>request restriction of certain processing; and</li>
                    <li>exercise other rights provided by applicable data-protection law.</li>
                </ul>
                <p>Migratepay may require reasonable identity verification before processing a privacy request.</p>
            </section>

            <section id="withdrawal-consent" class="terms-section">
                <h2>23. Withdrawal of Consent</h2>
                <p>Where Migratepay processes information based on consent, you may withdraw your consent where legally permitted.</p>
                <p>Withdrawal of consent does not affect processing that was lawfully carried out before the withdrawal.</p>
                <p>Migratepay may also continue processing information where another lawful basis permits or requires such processing.</p>
            </section>

            <section id="marketing-communications" class="terms-section">
                <h2>24. Marketing Communications</h2>
                <p>Where permitted by Applicable Law, Migratepay may send Users:</p>
                <ul>
                    <li>promotional offers;</li>
                    <li>service announcements;</li>
                    <li>product updates;</li>
                    <li>educational content;</li>
                    <li>new-feature announcements; and</li>
                    <li>other marketing communications.</li>
                </ul>
                <p>You may unsubscribe from marketing communications using the available unsubscribe mechanism or by contacting Migratepay.</p>
                <p>You may continue receiving essential communications relating to:</p>
                <ul>
                    <li>transactions;</li>
                    <li>payments;</li>
                    <li>settlements;</li>
                    <li>security;</li>
                    <li>account activity;</li>
                    <li>legal matters; and</li>
                    <li>important service changes.</li>
                </ul>
            </section>

            <section id="third-party-providers" class="terms-section">
                <h2>25. Third-Party Service Providers</h2>
                <p>Migratepay may engage third-party service providers for:</p>
                <ul>
                    <li>payment processing;</li>
                    <li>banking services;</li>
                    <li>identity verification;</li>
                    <li>hosting;</li>
                    <li>cloud storage;</li>
                    <li>cybersecurity;</li>
                    <li>analytics;</li>
                    <li>communication;</li>
                    <li>customer support;</li>
                    <li>accounting;</li>
                    <li>auditing; and</li>
                    <li>other operational functions.</li>
                </ul>
                <p>These providers may process information on behalf of Migratepay where necessary to provide their services.</p>
                <p>Where required by Applicable Law, appropriate contractual or other safeguards may be implemented.</p>
            </section>

            <section id="international-transfers" class="terms-section">
                <h2>26. International Data Transfers</h2>
                <p>Some Migratepay service providers may operate or store information outside Bangladesh.</p>
                <p>Where personal information is transferred, stored or processed outside Bangladesh, Migratepay will take such steps as may be required under Applicable Law concerning lawful transfer, safeguards, authorization, consent, contractual protections or other applicable requirements.</p>
            </section>

            <section id="childrens-privacy" class="terms-section">
                <h2>27. Children's Privacy</h2>
                <p>Migratepay's services are intended for individuals who are legally capable of entering into applicable transactions.</p>
                <p>Migratepay does not knowingly collect personal information from children where such collection is prohibited by Applicable Law.</p>
                <p>If Migratepay becomes aware that information has been collected in violation of applicable requirements, we may take reasonable steps to delete or restrict that information.</p>
            </section>

            <section id="sensitive-info" class="terms-section">
                <h2>28. Sensitive Personal Information</h2>
                <p>Certain information may receive enhanced protection under Applicable Law.</p>
                <p>This may include information relating to:</p>
                <ul>
                    <li>identity;</li>
                    <li>financial information;</li>
                    <li>authentication information;</li>
                    <li>biometric information;</li>
                    <li>health information; and</li>
                    <li>other categories legally designated as sensitive or protected information.</li>
                </ul>
                <p>Migratepay will process such information only where reasonably necessary and on a lawful basis.</p>
                <p>Where additional safeguards, authorization or consent are required, Migratepay will implement them as required by Applicable Law.</p>
            </section>

            <section id="analytics-usage" class="terms-section">
                <h2>29. Analytics and Website Usage</h2>
                <p>Migratepay may use analytics technologies to understand:</p>
                <ul>
                    <li>website traffic;</li>
                    <li>page usage;</li>
                    <li>feature usage;</li>
                    <li>user interaction;</li>
                    <li>technical errors;</li>
                    <li>website performance; and</li>
                    <li>general usage trends.</li>
                </ul>
                <p>Analytics information may be aggregated, anonymized or otherwise processed in accordance with Applicable Law where appropriate.</p>
            </section>

            <section id="log-files" class="terms-section">
                <h2>30. Log Files</h2>
                <p>Migratepay's systems may automatically generate technical logs containing information such as:</p>
                <ul>
                    <li>IP address;</li>
                    <li>access time;</li>
                    <li>browser information;</li>
                    <li>device information;</li>
                    <li>requested pages;</li>
                    <li>system events;</li>
                    <li>error information; and</li>
                    <li>security-related information.</li>
                </ul>
                <p>Such information may be used for:</p>
                <ul>
                    <li>security;</li>
                    <li>system administration;</li>
                    <li>troubleshooting;</li>
                    <li>fraud prevention;</li>
                    <li>performance monitoring; and</li>
                    <li>legal compliance.</li>
                </ul>
            </section>

            <section id="fraud-investigations" class="terms-section">
                <h2>31. Fraud and Security Investigations</h2>
                <p>Migratepay may process and retain information where reasonably necessary to investigate:</p>
                <ul>
                    <li>fraud;</li>
                    <li>attempted fraud;</li>
                    <li>unauthorized transactions;</li>
                    <li>Account takeover;</li>
                    <li>identity theft;</li>
                    <li>suspicious transaction activity;</li>
                    <li>abuse of Migratepay;</li>
                    <li>cyber incidents;</li>
                    <li>payment disputes; or</li>
                    <li>other security concerns.</li>
                </ul>
                <p>This may involve analyzing Account activity, transaction patterns, device information, communications and other relevant information.</p>
            </section>

            <section id="legal-disclosure" class="terms-section">
                <h2>32. Legal Disclosure</h2>
                <p>Migratepay may disclose information where we reasonably believe disclosure is necessary or appropriate to:</p>
                <ul>
                    <li>comply with Applicable Law;</li>
                    <li>comply with a court order;</li>
                    <li>comply with a lawful government request;</li>
                    <li>respond to a regulator;</li>
                    <li>cooperate with law enforcement;</li>
                    <li>protect the rights or safety of Migratepay or others;</li>
                    <li>investigate suspected fraud or unlawful conduct;</li>
                    <li>establish or defend legal claims; or</li>
                    <li>protect the security and integrity of the Platform.</li>
                </ul>
            </section>

            <section id="business-transfers" class="terms-section">
                <h2>33. Business Transfers</h2>
                <p>If Migratepay Digital Solution is involved in a merger, acquisition, restructuring, financing, sale of assets or other business transaction, personal information may form part of the relevant transferred business assets.</p>
                <p>Any such transfer will be handled subject to Applicable Law.</p>
            </section>

            <section id="third-party-websites" class="terms-section">
                <h2>34. Third-Party Websites</h2>
                <p>Migratepay may contain links or integrations to third-party websites or services.</p>
                <p>Migratepay Digital Solution is not responsible for the privacy practices, security, content or policies of independent third parties.</p>
                <p>Users should review the privacy policy of any third-party service before providing personal information to that service.</p>
            </section>

            <section id="no-absolute-security" class="terms-section">
                <h2>35. No Absolute Security Guarantee</h2>
                <p>Although Migratepay takes reasonable measures to protect personal information, Users acknowledge that:</p>
                <ul>
                    <li>Internet transmission is not completely secure;</li>
                    <li>electronic storage carries inherent risks;</li>
                    <li>third-party systems may experience security incidents;</li>
                    <li>cyber threats may evolve; and</li>
                    <li>no organization can guarantee absolute security.</li>
                </ul>
                <p>To the maximum extent permitted by Applicable Law, Migratepay Digital Solution shall not be responsible for security incidents caused solely by circumstances beyond its reasonable control or by unlawful acts of third parties, except where liability cannot lawfully be excluded or limited.</p>
            </section>

            <section id="data-breaches" class="terms-section">
                <h2>36. Data Breaches and Security Incidents</h2>
                <p>If Migratepay becomes aware of a personal-data breach or other security incident that triggers notification or other obligations under Applicable Law, Migratepay will take reasonable steps to:</p>
                <ul>
                    <li>investigate the incident;</li>
                    <li>contain the incident;</li>
                    <li>mitigate potential harm;</li>
                    <li>improve security measures; and</li>
                    <li>make legally required notifications to affected individuals and/or competent authorities.</li>
                </ul>
            </section>

            <section id="compliance-bd-law" class="terms-section">
                <h2>37. Compliance With Bangladesh Law</h2>
                <p>Migratepay Digital Solution intends to process personal information in accordance with Applicable Law.</p>
                <p>This includes applicable Bangladesh requirements relating to:</p>
                <ul>
                    <li>personal-data protection;</li>
                    <li>data management;</li>
                    <li>cybersecurity;</li>
                    <li>electronic transactions;</li>
                    <li>digital commerce;</li>
                    <li>taxation;</li>
                    <li>financial transactions;</li>
                    <li>consumer protection; and</li>
                    <li>other applicable legal and regulatory requirements.</li>
                </ul>
                <p>Bangladesh's current framework includes the Personal Data Protection Act, 2026 and National Data Management Act, 2026, both officially published by the Information and Communication Technology Division in April 2026.</p>
                <p>Migratepay may update its data-processing practices and this Privacy Policy when legislation, regulatory requirements or business operations change.</p>
            </section>

            <section id="changes-privacy-policy" class="terms-section">
                <h2>38. Changes to This Privacy Policy</h2>
                <p>Migratepay Digital Solution may update this Privacy Policy from time to time.</p>
                <p>Updates may be made because of:</p>
                <ul>
                    <li>changes to our services;</li>
                    <li>changes in technology;</li>
                    <li>changes in business operations;</li>
                    <li>changes in legislation;</li>
                    <li>regulatory developments;</li>
                    <li>security requirements; or</li>
                    <li>changes in our data-processing practices.</li>
                </ul>
                <p>The updated version will be published on this page with a revised “Last Updated” date.</p>
                <p>Where required by Applicable Law, Migratepay may provide additional notice or obtain additional consent.</p>
            </section>

            <section id="privacy-requests" class="terms-section">
                <h2>39. Privacy Requests and Contact</h2>
                <p>For privacy-related questions, requests or complaints, please contact:</p>
                <p><strong>Migratepay Digital Solution</strong></p>
                <ul>
                    <li><strong>Legal Entity Name:</strong> Migratepay Digital Solution</li>
                    <li><strong>Registered/Principal Address:</strong> [Full Business Address]</li>
                    <li><strong>Privacy Contact:</strong> [Name/Designation]</li>
                    <li><strong>Privacy Email:</strong> privacy@migratepay.com</li>
                    <li><strong>General Support:</strong> support@migratepay.com</li>
                    <li><strong>Telephone:</strong> [Phone Number]</li>
                    <li><strong>Website:</strong> [Website URL]</li>
                </ul>
                <p>Migratepay may require reasonable information to verify your identity before processing a privacy-related request.</p>
            </section>

            <section id="privacy-complaints" class="terms-section">
                <h2>40. Privacy Complaints</h2>
                <p>If you believe that Migratepay has handled your personal information in a manner inconsistent with this Privacy Policy or Applicable Law, you may contact us using the contact information provided above.</p>
                <p>Migratepay will review legitimate complaints and take reasonable steps to address privacy concerns.</p>
                <p>Nothing in this section prevents you from exercising any right you may have to contact a competent regulatory authority, government body, court or other lawful forum.</p>
            </section>

            <section id="governing-law" class="terms-section">
                <h2>41. Governing Law</h2>
                <p>This Privacy Policy shall be governed by and interpreted in accordance with the laws of Bangladesh, subject to any mandatory rights or protections applicable under Applicable Law.</p>
            </section>

            <section id="acceptance" class="terms-section">
                <h2>42. Acceptance</h2>
                <p>By accessing or using Migratepay, you acknowledge that you have read and understood this Privacy Policy.</p>
                <p>Where Applicable Law requires express consent for a particular processing activity, Migratepay Digital Solution will obtain such consent through an appropriate mechanism.</p>
            </section>

            <section id="summary" class="terms-section">
                <div class="clause-important">
                    <strong>Privacy Policy Summary</strong>
                    <p style="margin-bottom: 12px;">For convenience:</p>
                    <p style="margin-bottom: 4px;"><strong>Migratepay collects:</strong><br>Account, identity, transaction, payment-related, settlement, technical, communication and verification information.</p>
                    <p style="margin-bottom: 4px;"><strong>Migratepay uses information for:</strong><br>Providing services, facilitating transactions, processing payments and settlements, security, fraud prevention, dispute resolution, customer support, legal compliance and improving the Platform.</p>
                    <p style="margin-bottom: 4px;"><strong>Migratepay may share information with:</strong><br>Payment providers, banks, verification providers, technology providers, professional advisers and government or regulatory authorities where legally required or otherwise permitted.</p>
                    <p style="margin-bottom: 4px;"><strong>Migratepay does not intend to:</strong><br>Sell personal information simply as a commercial commodity.</p>
                    <p style="margin-bottom: 4px;"><strong>Migratepay may retain information:</strong><br>For as long as reasonably necessary and, where required or permitted, for legal, tax, accounting, security, regulatory and dispute-related purposes.</p>
                    <p><strong>Migratepay protects information:</strong><br>Through reasonable technical and organizational safeguards, while recognizing that no electronic system can guarantee absolute security.</p>
                </div>
            </section>
            
            <div class="terms-footer-cta">
                <h2>Have questions about our privacy policy?</h2>
                <p>Contact our team if you need clarification regarding our data practices.</p>
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
