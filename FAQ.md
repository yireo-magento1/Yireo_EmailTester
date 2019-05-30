# How do you recommend to test emails?
The EmailTester extension makes it simple to test emails. Start with the basics of your email - the basic layout, your company logo, and content. This can all be tested easily by using the preview page in the Magento Admin Panel. Once you're done with these basics, you can test how your emails behave under different email-clients (Outlook, Gmail, Thunderbird, etc) by sending them to your test-accounts.

# Magento already has an email-preview. Why do I need this?
Magento already features a way to preview transactional emails. However, there are a couple of downsides to this feature: First of all, you need to create a new instance of any default template before you can actually preview it. Second, the preview does not allow you to insert real-life data - things like the logo, customer-name, order-table will just appear empty. Third, it does not allow you to send a preview as an actual email. All three of these problems are solved with our extension.

# Can I extend the variables that are inserted into emails?
Yes, you can. The EmailTester extension dispatches an event
emailtester_variables that allows third party developers to hook into
the test-procedure and insert their own variables. Declare your own
observer in XML for this event, and use your Observer-class to add
variables.

# Can I configure the sender of emails?
Yes, but this is not configured in EmailTester. It is configured via the System Configuration > Store Email Addresses > General Contact > Sender Email (trans_email/ident_general/email).

# No date part in (empty)
We have encountered some cases where Magento would generate an error No date part in and then some value. In all of these cases, the mail template that was being sent was relying on data not present in the selected values. For instance, when you try to send an email New Invoice, this assumes that the order you have selected indeed has a valid invoice. Selecting an order with valid invoice fixes the issue.

# Does this work with Magento third party extensions?
The EmailTester extension works together with other Magento extensions, but it just depends how these Magento extensions are hooking into the Magento email-functionality. If a third party module adds a new transactional email to Magento, that email will be picked up upon by EmailTester. If a third party module inserts additional information into existing transactional emails, this again will work with EmailTester. However, if a third party module inserts its own variables, those variables will not be filled up with dummy content by EmailTester. If this happens, mail us and we'll see how we can enhance EmailTester quickly.

# Output is garbled. What to do?
When EmailTester shows a preview of an email in the browser, it encodes the page using UTF-8. It might be that the email itself is formatted using a different encoding. This is a bad practice. It is best to convert all Magento content to UTF-8 (because that's used by the Magento core, the Magento database, and most likely all other webserver-components as well). If in doubt, make sure to contact us.
