# Reset Database
TRUNCATE TABLE mlm_ewallet;
TRUNCATE TABLE mlm_transactions;
UPDATE mlm_registrations set mlm_registrations.wallet_money = 0, mlm_registrations.wallet_total = 0;
UPDATE rb_purchases set rb_purchases.record_check = 0, rb_purchases.repurchase_check = 0, invoicedate = '', tracking_status = 'ordered';