@extends('layouts.frontend')
@section('title', 'Cookies Policy | Migratepay')

@section('content')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
<script src="{{ asset('js/legal-pages.js') }}"></script>

<div class="terms-page">
    <div class="terms-hero">
        <h1>COOKIES POLICY</h1>
        <p>Migratepay Digital Solution uses cookies and similar technologies on the Migratepay website, platform and related digital services.</p>
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
                <li><a href="#what-are-cookies" class="toc-link">1. What Are Cookies?</a></li>
                <li><a href="#similar-technologies" class="toc-link">2. Similar Technologies</a></li>
                <li><a href="#why-migratepay-uses-cookies" class="toc-link">3. Why Migratepay Uses Cookies</a></li>
                <li><a href="#types-of-cookies" class="toc-link">4. Types of Cookies We Use</a></li>
                <li><a href="#strictly-necessary" class="toc-link">5. Strictly Necessary Cookies</a></li>
                <li><a href="#authentication-account" class="toc-link">6. Authentication and Account Cookies</a></li>
                <li><a href="#transaction-session" class="toc-link">7. Transaction and Session Cookies</a></li>
                <li><a href="#security-fraud" class="toc-link">8. Security and Fraud Prevention Cookies</a></li>
                <li><a href="#functional-cookies" class="toc-link">9. Functional Cookies</a></li>
                <li><a href="#preference-cookies" class="toc-link">10. Preference Cookies</a></li>
                <li><a href="#analytics-performance" class="toc-link">11. Analytics and Performance Cookies</a></li>
                <li><a href="#marketing-advertising" class="toc-link">12. Marketing and Advertising Cookies</a></li>
                <li><a href="#third-party" class="toc-link">13. Third-Party Cookies</a></li>
                <li><a href="#payment-technologies" class="toc-link">14. Payment-Related Technologies</a></li>
                <li><a href="#cookie-duration" class="toc-link">15. Cookie Duration</a></li>
                <li><a href="#first-third-party" class="toc-link">16. First-Party and Third-Party Cookies</a></li>
                <li><a href="#cookie-consent" class="toc-link">17. Cookie Consent</a></li>
                <li><a href="#changing-preferences" class="toc-link">18. Changing Your Cookie Preferences</a></li>
                <li><a href="#browser-controls" class="toc-link">19. Browser Cookie Controls</a></li>
                <li><a href="#if-you-disable" class="toc-link">20. What Happens If You Disable Cookies?</a></li>
                <li><a href="#do-not-track" class="toc-link">21. Do Not Track Signals</a></li>
                <li><a href="#cookies-personal-info" class="toc-link">22. Cookies and Personal Information</a></li>
                <li><a href="#security-cookie-info" class="toc-link">23. Security of Cookie Information</a></li>
                <li><a href="#fraud-prevention" class="toc-link">24. Cookies Used for Fraud Prevention</a></li>
                <li><a href="#account-protection" class="toc-link">25. Cookies Used for Account Protection</a></li>
                <li><a href="#website-improvement" class="toc-link">26. Cookies Used for Website Improvement</a></li>
                <li><a href="#marketing-measurement" class="toc-link">27. Cookies Used for Marketing Measurement</a></li>
                <li><a href="#third-party-privacy" class="toc-link">28. Third-Party Privacy Policies</a></li>
                <li><a href="#changes-policy" class="toc-link">29. Changes to This Cookies Policy</a></li>
                <li><a href="#relationship-privacy" class="toc-link">30. Relationship With Our Privacy Policy</a></li>
                <li><a href="#contact" class="toc-link">31. Contact Us</a></li>
                <li><a href="#governing-law" class="toc-link">32. Governing Law</a></li>
                <li><a href="#acceptance" class="toc-link">33. Acceptance</a></li>
            </ul>
        </aside>

        <!-- Right Content -->
        <div class="terms-content">
            <section class="terms-section" style="margin-top:-30px;">
                <p>This Cookies Policy explains what cookies are, how Migratepay uses them, the different types of cookies that may be used, why we use them, how third parties may use cookies, and how you can manage your cookie preferences.</p>
                <p>This Cookies Policy should be read together with the Migratepay Privacy Policy and Terms & Conditions.</p>
                <p>By continuing to use Migratepay after being presented with a cookie notice, where applicable, you may consent to the use of cookies in accordance with your selected preferences and Applicable Law.</p>
            </section>

            <section id="what-are-cookies" class="terms-section">
                <h2>1. What Are Cookies?</h2>
                <p>Cookies are small text files that websites may store on your device when you visit a website.</p>
                <p>Cookies allow a website to recognize your browser or device and remember certain information about your previous activity.</p>
                <p>Cookies can help websites:</p>
                <ul>
                    <li>operate properly;</li>
                    <li>remember preferences;</li>
                    <li>maintain user sessions;</li>
                    <li>improve security;</li>
                    <li>understand how visitors use the website;</li>
                    <li>improve performance;</li>
                    <li>personalize certain experiences; and</li>
                    <li>provide relevant communications or advertising where permitted.</li>
                </ul>
                <p>Cookies may be session cookies, which are deleted when you close your browser, or persistent cookies, which remain on your device for a specified period or until you delete them.</p>
            </section>

            <section id="similar-technologies" class="terms-section">
                <h2>2. Similar Technologies</h2>
                <p>In addition to cookies, Migratepay may use other technologies that perform similar functions.</p>
                <p>These may include:</p>
                <ul>
                    <li>web beacons;</li>
                    <li>pixels;</li>
                    <li>tags;</li>
                    <li>local storage;</li>
                    <li>session storage;</li>
                    <li>software development kits;</li>
                    <li>device identifiers; and</li>
                    <li>similar tracking or storage technologies.</li>
                </ul>
                <p>For simplicity, this Cookies Policy refers to these technologies collectively as “Cookies” unless a distinction is necessary.</p>
            </section>

            <section id="why-migratepay-uses-cookies" class="terms-section">
                <h2>3. Why Migratepay Uses Cookies</h2>
                <p>Migratepay uses Cookies for legitimate purposes, including:</p>
                <ul>
                    <li>operating the website;</li>
                    <li>maintaining secure user sessions;</li>
                    <li>remembering preferences;</li>
                    <li>facilitating transactions;</li>
                    <li>maintaining transaction sessions;</li>
                    <li>preventing fraudulent activity;</li>
                    <li>detecting suspicious activity;</li>
                    <li>improving website performance;</li>
                    <li>understanding website usage;</li>
                    <li>analyzing technical problems;</li>
                    <li>improving our services;</li>
                    <li>measuring marketing performance where permitted;</li>
                    <li>providing relevant communications where permitted; and</li>
                    <li>complying with legal and regulatory requirements.</li>
                </ul>
            </section>

            <section id="types-of-cookies" class="terms-section">
                <h2>4. Types of Cookies We Use</h2>
                <p>Cookies used by Migratepay may generally fall into the following categories:</p>
                <ul>
                    <li>Strictly Necessary Cookies</li>
                    <li>Security and Fraud Prevention Cookies</li>
                    <li>Functional Cookies</li>
                    <li>Preference Cookies</li>
                    <li>Analytics and Performance Cookies</li>
                    <li>Marketing and Advertising Cookies</li>
                    <li>Third-Party Cookies</li>
                </ul>
                <p>Not every category will necessarily be used at all times.</p>
                <p>The actual Cookies used by Migratepay may change as our website, services and technology develop.</p>
            </section>

            <section id="strictly-necessary" class="terms-section">
                <h2>5. Strictly Necessary Cookies</h2>
                <p>Strictly Necessary Cookies are required for certain essential website functions.</p>
                <p>These Cookies may help Migratepay:</p>
                <ul>
                    <li>maintain user sessions;</li>
                    <li>authenticate users;</li>
                    <li>keep users logged in;</li>
                    <li>maintain transaction sessions;</li>
                    <li>remember security-related settings;</li>
                    <li>maintain shopping or transaction functionality;</li>
                    <li>protect against unauthorized activity;</li>
                    <li>provide secure access to restricted areas; and</li>
                    <li>operate essential Platform features.</li>
                </ul>
                <p>Without these Cookies, certain Migratepay services may not function correctly.</p>
                <p>Because these Cookies may be necessary for the operation and security of the Platform, they may not always be capable of being disabled through the cookie-preference system.</p>
            </section>

            <section id="authentication-account" class="terms-section">
                <h2>6. Authentication and Account Cookies</h2>
                <p>When you log into a Migratepay Account, Cookies may be used to maintain your authenticated session.</p>
                <p>These Cookies may help us determine:</p>
                <ul>
                    <li>whether you are logged in;</li>
                    <li>which Account is currently authenticated;</li>
                    <li>whether your session remains valid;</li>
                    <li>whether authentication information is still valid; and</li>
                    <li>whether access to a particular section of the Platform is authorized.</li>
                </ul>
                <p>These Cookies help reduce the need to repeatedly authenticate during a session.</p>
            </section>

            <section id="transaction-session" class="terms-section">
                <h2>7. Transaction and Session Cookies</h2>
                <p>Because Migratepay facilitates transactions between Buyers and Sellers, certain Cookies may be necessary to maintain transaction-related sessions.</p>
                <p>These Cookies may help maintain:</p>
                <ul>
                    <li>transaction sessions;</li>
                    <li>transaction forms;</li>
                    <li>payment-related sessions;</li>
                    <li>checkout or confirmation processes;</li>
                    <li>selected transaction information;</li>
                    <li>temporary transaction states; and</li>
                    <li>other essential Platform functionality.</li>
                </ul>
                <p>These Cookies may be particularly important when you move between different pages during a transaction.</p>
            </section>

            <section id="security-fraud" class="terms-section">
                <h2>8. Security and Fraud Prevention Cookies</h2>
                <p>Migratepay may use Cookies and similar technologies to protect Users and the Platform against unauthorized or suspicious activity.</p>
                <p>These technologies may help us:</p>
                <ul>
                    <li>detect unusual activity;</li>
                    <li>identify suspicious sessions;</li>
                    <li>prevent automated abuse;</li>
                    <li>detect attempted Account takeover;</li>
                    <li>protect login functionality;</li>
                    <li>identify potentially fraudulent behavior;</li>
                    <li>investigate security incidents; and</li>
                    <li>maintain Platform security.</li>
                </ul>
                <p>Security-related Cookies may collect technical information such as browser characteristics, device information, session information and other security-related signals.</p>
            </section>

            <section id="functional-cookies" class="terms-section">
                <h2>9. Functional Cookies</h2>
                <p>Functional Cookies allow Migratepay to remember information that improves the functionality of the Platform.</p>
                <p>For example, they may remember:</p>
                <ul>
                    <li>language preferences;</li>
                    <li>selected settings;</li>
                    <li>interface preferences;</li>
                    <li>recently selected options;</li>
                    <li>form preferences; and</li>
                    <li>other customization choices.</li>
                </ul>
                <p>Disabling Functional Cookies may cause some features to operate differently.</p>
            </section>

            <section id="preference-cookies" class="terms-section">
                <h2>10. Preference Cookies</h2>
                <p>Preference Cookies allow Migratepay to remember choices you have previously made.</p>
                <p>These may include:</p>
                <ul>
                    <li>language;</li>
                    <li>region;</li>
                    <li>display preferences;</li>
                    <li>cookie preferences;</li>
                    <li>notification preferences; and</li>
                    <li>other user-selected settings.</li>
                </ul>
                <p>Preference Cookies help provide a more consistent experience when you return to Migratepay.</p>
            </section>

            <section id="analytics-performance" class="terms-section">
                <h2>11. Analytics and Performance Cookies</h2>
                <p>Migratepay may use analytics and performance Cookies to understand how visitors interact with our website.</p>
                <p>These Cookies may help us understand:</p>
                <ul>
                    <li>how many visitors use the website;</li>
                    <li>which pages receive the most traffic;</li>
                    <li>how users navigate the Platform;</li>
                    <li>how long users spend on particular pages;</li>
                    <li>which features are used;</li>
                    <li>whether pages load correctly;</li>
                    <li>where technical problems occur;</li>
                    <li>how the website performs; and</li>
                    <li>how the Platform can be improved.</li>
                </ul>
                <p>Analytics information may be aggregated or otherwise processed to understand general usage trends.</p>
                <p>Where appropriate, Migratepay may take measures designed to reduce the identification of individual Users.</p>
            </section>

            <section id="marketing-advertising" class="terms-section">
                <h2>12. Marketing and Advertising Cookies</h2>
                <p>Where permitted by Applicable Law, Migratepay may use marketing or advertising technologies to:</p>
                <ul>
                    <li>understand the effectiveness of marketing campaigns;</li>
                    <li>measure advertising performance;</li>
                    <li>identify general audience trends;</li>
                    <li>display relevant promotional content;</li>
                    <li>limit repetitive advertisements;</li>
                    <li>measure conversions; and</li>
                    <li>understand how visitors interact with promotional content.</li>
                </ul>
                <p>Marketing Cookies may be placed by Migratepay or by authorized third-party service providers.</p>
                <p>Where consent is legally required for such Cookies, Migratepay will seek the appropriate consent before using them.</p>
            </section>

            <section id="third-party" class="terms-section">
                <h2>13. Third-Party Cookies</h2>
                <p>Some Cookies may be placed by third parties whose services are used by Migratepay.</p>
                <p>These third parties may include providers of:</p>
                <ul>
                    <li>analytics services;</li>
                    <li>payment services;</li>
                    <li>security services;</li>
                    <li>hosting and infrastructure;</li>
                    <li>customer-support systems;</li>
                    <li>communication services;</li>
                    <li>marketing services;</li>
                    <li>advertising services;</li>
                    <li>embedded content; and</li>
                    <li>other technology services.</li>
                </ul>
                <p>Third-party Cookies are controlled by the respective third party.</p>
                <p>Their use of Cookies may be governed by their own privacy and cookie policies.</p>
                <p>Migratepay does not control the Cookie practices of independent third parties.</p>
            </section>

            <section id="payment-technologies" class="terms-section">
                <h2>14. Payment-Related Technologies</h2>
                <p>When you use a payment service through Migratepay, the applicable payment provider may use Cookies or similar technologies for purposes such as:</p>
                <ul>
                    <li>maintaining payment sessions;</li>
                    <li>authenticating transactions;</li>
                    <li>preventing fraud;</li>
                    <li>securing payment processes;</li>
                    <li>detecting suspicious activity;</li>
                    <li>maintaining payment functionality; and</li>
                    <li>complying with applicable payment-security requirements.</li>
                </ul>
                <p>Payment providers may process technical or payment-related information in accordance with their own privacy policies and terms.</p>
                <p>Migratepay does not control the internal Cookie practices of independent payment providers.</p>
            </section>

            <section id="cookie-duration" class="terms-section">
                <h2>15. Cookie Duration</h2>
                <p>Cookies may remain on your device for different periods.</p>
                <h3>Session Cookies</h3>
                <p>These generally expire when you close your browser.</p>
                <h3>Persistent Cookies</h3>
                <p>These remain on your device for a specified period or until they are manually deleted.</p>
                <p>The duration of a Cookie depends on:</p>
                <ul>
                    <li>its purpose;</li>
                    <li>the technology provider;</li>
                    <li>security requirements;</li>
                    <li>applicable configuration; and</li>
                    <li>other technical considerations.</li>
                </ul>
            </section>

            <section id="first-third-party" class="terms-section">
                <h2>16. First-Party and Third-Party Cookies</h2>
                <h3>First-Party Cookies</h3>
                <p>These are Cookies placed directly by Migratepay.</p>
                <p>They may be used for:</p>
                <ul>
                    <li>authentication;</li>
                    <li>account functionality;</li>
                    <li>security;</li>
                    <li>preferences;</li>
                    <li>transaction functionality;</li>
                    <li>website operation; and</li>
                    <li>analytics.</li>
                </ul>
                <h3>Third-Party Cookies</h3>
                <p>These are Cookies placed by a third-party service provider operating through or in connection with Migratepay.</p>
                <p>They may be used for:</p>
                <ul>
                    <li>analytics;</li>
                    <li>payment processing;</li>
                    <li>security;</li>
                    <li>marketing;</li>
                    <li>advertising;</li>
                    <li>customer support; or</li>
                    <li>other services.</li>
                </ul>
            </section>

            <section id="cookie-consent" class="terms-section">
                <h2>17. Cookie Consent</h2>
                <p>Where Applicable Law requires consent before certain Cookies are placed or used, Migratepay may display a Cookie consent mechanism.</p>
                <p>Depending on the available options, you may be able to:</p>
                <ul>
                    <li>accept all Cookies;</li>
                    <li>reject non-essential Cookies;</li>
                    <li>select specific Cookie categories;</li>
                    <li>change your Cookie preferences; or</li>
                    <li>withdraw previously provided consent.</li>
                </ul>
                <p>Strictly Necessary Cookies may continue to operate where they are necessary to provide requested functionality, maintain security or comply with applicable legal requirements.</p>
            </section>

            <section id="changing-preferences" class="terms-section">
                <h2>18. Changing Your Cookie Preferences</h2>
                <p>Where a Cookie Preference Center is available on Migratepay, you may use it to modify your preferences.</p>
                <p>Your available options may include:</p>
                <ul>
                    <li><strong>Necessary Cookies:</strong> These are required for essential Platform functionality.</li>
                    <li><strong>Functional Cookies:</strong> Used to remember preferences and improve functionality.</li>
                    <li><strong>Analytics Cookies:</strong> Used to understand website usage and performance.</li>
                    <li><strong>Marketing Cookies:</strong> Used for marketing and advertising purposes where permitted.</li>
                </ul>
                <p>Your preferences may not immediately affect Cookies that have already been stored on your device. You may need to delete existing Cookies through your browser or device settings.</p>
            </section>

            <section id="browser-controls" class="terms-section">
                <h2>19. Browser Cookie Controls</h2>
                <p>Most modern web browsers allow you to:</p>
                <ul>
                    <li>view Cookies;</li>
                    <li>delete Cookies;</li>
                    <li>block Cookies;</li>
                    <li>restrict Cookies;</li>
                    <li>receive notifications before Cookies are stored; and</li>
                    <li>manage certain website-specific Cookie settings.</li>
                </ul>
                <p>The method for managing Cookies differs between browsers.</p>
                <p>You should consult your browser's official documentation for instructions.</p>
                <p>Please note that disabling Cookies may cause certain Migratepay features to stop working correctly.</p>
            </section>

            <section id="if-you-disable" class="terms-section">
                <h2>20. What Happens If You Disable Cookies?</h2>
                <p>If you disable all Cookies, certain parts of Migratepay may not function properly.</p>
                <p>For example, disabling necessary Cookies may affect:</p>
                <ul>
                    <li>Account login;</li>
                    <li>authentication;</li>
                    <li>transaction sessions;</li>
                    <li>payment-related functionality;</li>
                    <li>security features;</li>
                    <li>user preferences;</li>
                    <li>forms;</li>
                    <li>navigation; and</li>
                    <li>other Platform functionality.</li>
                </ul>
                <p>You can generally disable non-essential Cookies while allowing necessary Cookies to remain active.</p>
            </section>

            <section id="do-not-track" class="terms-section">
                <h2>21. Do Not Track Signals</h2>
                <p>Some browsers and devices provide a “Do Not Track” or similar browser setting.</p>
                <p>Because there is currently no universally consistent technical standard governing how websites should respond to all such signals, Migratepay may not respond to every browser-based Do Not Track signal.</p>
                <p>Where required by Applicable Law, Migratepay will handle applicable tracking preferences in accordance with legal requirements.</p>
            </section>

            <section id="cookies-personal-info" class="terms-section">
                <h2>22. Cookies and Personal Information</h2>
                <p>Some Cookies may collect information that can be associated with an identifiable User or device.</p>
                <p>Examples may include:</p>
                <ul>
                    <li>IP address;</li>
                    <li>Account identifier;</li>
                    <li>device identifier;</li>
                    <li>browser information;</li>
                    <li>session identifier;</li>
                    <li>online activity; and</li>
                    <li>other technical information.</li>
                </ul>
                <p>Where such information constitutes personal information under Applicable Law, Migratepay will process it in accordance with our Privacy Policy and applicable data-protection requirements.</p>
            </section>

            <section id="security-cookie-info" class="terms-section">
                <h2>23. Security of Cookie Information</h2>
                <p>Migratepay takes reasonable technical and organizational measures to protect information collected through Cookies.</p>
                <p>However, no Internet-based technology can be guaranteed to be completely secure.</p>
                <p>Users should therefore maintain appropriate security on their devices and browsers and avoid using Migratepay through compromised or unauthorized devices.</p>
            </section>

            <section id="fraud-prevention" class="terms-section">
                <h2>24. Cookies Used for Fraud Prevention</h2>
                <p>Certain Cookies may be essential to the security of Migratepay.</p>
                <p>Migratepay may use Cookies to help identify:</p>
                <ul>
                    <li>unusual login patterns;</li>
                    <li>repeated failed authentication attempts;</li>
                    <li>suspicious devices;</li>
                    <li>automated activity;</li>
                    <li>abnormal transaction behavior;</li>
                    <li>Account takeover attempts; and</li>
                    <li>other potentially fraudulent activity.</li>
                </ul>
                <p>Such information may be combined with other technical and transaction information where reasonably necessary for security and fraud-prevention purposes.</p>
            </section>

            <section id="account-protection" class="terms-section">
                <h2>25. Cookies Used for Account Protection</h2>
                <p>Migratepay may use Cookies to help protect User Accounts.</p>
                <p>These may help determine whether:</p>
                <ul>
                    <li>an authenticated session is valid;</li>
                    <li>a User is authorized to access a particular area;</li>
                    <li>a session has expired;</li>
                    <li>unusual login activity has occurred; or</li>
                    <li>additional security verification may be necessary.</li>
                </ul>
            </section>

            <section id="website-improvement" class="terms-section">
                <h2>26. Cookies Used for Website Improvement</h2>
                <p>Migratepay may analyze Cookie-related information to improve:</p>
                <ul>
                    <li>website design;</li>
                    <li>page speed;</li>
                    <li>navigation;</li>
                    <li>transaction flows;</li>
                    <li>account management;</li>
                    <li>usability;</li>
                    <li>accessibility;</li>
                    <li>Platform reliability; and</li>
                    <li>overall User experience.</li>
                </ul>
            </section>

            <section id="marketing-measurement" class="terms-section">
                <h2>27. Cookies Used for Marketing Measurement</h2>
                <p>Where permitted, Migratepay may use Cookies to measure whether marketing campaigns are effective.</p>
                <p>For example, we may measure:</p>
                <ul>
                    <li>visits generated by campaigns;</li>
                    <li>registrations;</li>
                    <li>general conversion activity;</li>
                    <li>campaign performance;</li>
                    <li>promotional engagement; and</li>
                    <li>website interactions following marketing communications.</li>
                </ul>
                <p>Where legally required, marketing and advertising Cookies will only be used after obtaining the necessary consent.</p>
            </section>

            <section id="third-party-privacy" class="terms-section">
                <h2>28. Third-Party Privacy Policies</h2>
                <p>Third-party services operating on or through Migratepay may use their own Cookies and similar technologies.</p>
                <p>Examples may include:</p>
                <ul>
                    <li>analytics providers;</li>
                    <li>payment providers;</li>
                    <li>advertising providers;</li>
                    <li>security providers;</li>
                    <li>customer-support platforms; and</li>
                    <li>embedded services.</li>
                </ul>
                <p>Migratepay recommends reviewing the privacy and cookie policies of such providers for more information regarding their practices.</p>
            </section>

            <section id="changes-policy" class="terms-section">
                <h2>29. Changes to This Cookies Policy</h2>
                <p>Migratepay Digital Solution may update this Cookies Policy from time to time.</p>
                <p>We may update it to reflect:</p>
                <ul>
                    <li>changes to our website;</li>
                    <li>new technologies;</li>
                    <li>changes in Cookies;</li>
                    <li>changes in third-party services;</li>
                    <li>changes in business operations;</li>
                    <li>changes in legal requirements;</li>
                    <li>regulatory developments; or</li>
                    <li>changes in our data-processing practices.</li>
                </ul>
                <p>The updated version will be published on this page with a revised “Last Updated” date.</p>
                <p>Where legally required, we may provide additional notice or obtain renewed consent.</p>
            </section>

            <section id="relationship-privacy" class="terms-section">
                <h2>30. Relationship With Our Privacy Policy</h2>
                <p>This Cookies Policy explains specifically how Migratepay uses Cookies and similar technologies.</p>
                <p>Our Privacy Policy explains more broadly how Migratepay Digital Solution collects, uses, stores, processes, shares and protects personal information.</p>
                <p>If information collected through Cookies constitutes personal information, the relevant processing will also be governed by our Privacy Policy.</p>
            </section>

            <section id="contact" class="terms-section">
                <h2>31. Contact Us</h2>
                <p>If you have questions regarding this Cookies Policy or how Migratepay uses Cookies, you may contact us at:</p>
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
            </section>

            <section id="governing-law" class="terms-section">
                <h2>32. Governing Law</h2>
                <p>This Cookies Policy shall be governed by and interpreted in accordance with the laws of Bangladesh, subject to any mandatory rights or protections applicable under Applicable Law.</p>
            </section>

            <section id="acceptance" class="terms-section">
                <h2>33. Acceptance</h2>
                <p>By accessing or using Migratepay, you acknowledge that you have read and understood this Cookies Policy.</p>
                <p>Where consent is required by Applicable Law for the use of particular Cookies or similar technologies, Migratepay will obtain such consent through an appropriate mechanism.</p>
            </section>
            
            <div class="terms-footer-cta">
                <h2>Still have questions?</h2>
                <p>Contact our support team if you need further clarification regarding our cookie practices.</p>
                <a href="/contact" class="btn-contact">Contact Support &rarr;</a>
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
