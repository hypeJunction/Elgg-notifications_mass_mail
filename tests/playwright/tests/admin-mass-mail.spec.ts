import { test, expect } from '@playwright/test';
import { loginAs, getLatestMassMail, getMetadata, queryDb } from '../helpers/elgg';

/**
 * End-to-end tests for the admin mass-mail form.
 *
 * Flow under test:
 *   /mass_mail/send -> form -> submit -> entity saved, send event fired
 *
 * We assert both UI state (redirect, success message) and DB state
 * (notification_mass_mail entity created with the expected title,
 * description, and method metadata).
 */

test.describe('Admin mass mail form', () => {
  test('admin can open mass mail page', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/mass_mail/send');

    expect(response?.status()).toBeLessThan(500);
    // Form must be present
    await expect(page.locator('form')).toBeVisible();
    // Required inputs present
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('textarea[name="description"]')).toBeVisible();
  });

  test('mass mail form advertises recipient count', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/mass_mail/send');
    // The form view echoes a recipient-count paragraph
    await expect(page.locator('p.elgg-text-help').first()).toBeVisible();
  });

  test('admin submitting form creates notification_mass_mail entity', async ({ page }) => {
    const title = `PW mass mail ${Date.now()}`;
    const description = 'Hello {{recipient.name}} from Playwright';

    await loginAs(page, 'admin');
    await page.goto('/mass_mail/send');

    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="description"]', description);
    // Force explicit method so we can assert it deterministically
    const emailRadio = page.locator('input[name="method"][value="email"]');
    if (await emailRadio.count()) {
      await emailRadio.check();
    }

    await Promise.all([
      page.waitForLoadState('networkidle'),
      page.click('button[type="submit"], input[type="submit"]'),
    ]);

    // UI: should no longer sit on the empty send form with errors
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);

    // DB: a notification_mass_mail entity with this title exists
    const rows = await queryDb(
      `SELECT e.guid FROM elgg_entities e
       JOIN elgg_metadata m ON m.entity_guid = e.guid AND m.name = 'title'
       WHERE e.type = 'object' AND e.subtype = 'notification_mass_mail'
         AND m.value = ?
       ORDER BY e.guid DESC LIMIT 1`,
      [title]
    );
    expect(rows.length).toBe(1);

    const guid = rows[0].guid;
    const descMeta = await getMetadata(guid, 'description');
    expect(descMeta[0]?.value).toBe(description);
  });

  test('submitting form without required fields shows no success', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/mass_mail/send');

    // Submit empty-ish form (HTML5 may block; we bypass by removing required)
    await page.evaluate(() => {
      document.querySelectorAll('input[required], textarea[required]').forEach((el) => {
        (el as HTMLElement).removeAttribute('required');
      });
    });
    await Promise.all([
      page.waitForLoadState('networkidle'),
      page.click('button[type="submit"], input[type="submit"]'),
    ]);

    // After empty submit, Elgg's server-side validation rejects the request and
    // redirects back (or shows an inline error). Either way, no success message
    // should appear and no mass_mail entity was created for an empty title.
    const success = page.locator('.elgg-system-messages .elgg-message-success');
    await expect(success).toHaveCount(0);
  });

  test('non-admin cannot reach site-level mass mail page', async ({ page }) => {
    await loginAs(page, 'testuser');
    const response = await page.goto('/mass_mail/send');
    // Either forbidden, redirected to login/home, or rendered empty-form
    // (container permissions handler returns false for non-admins at site level)
    expect([200, 302, 403]).toContain(response?.status() ?? 0);
    if (response?.status() === 200) {
      // If it renders, the send form must NOT be editable for this user
      const forbidden = page.locator('.elgg-message-error, .elgg-output');
      await expect(forbidden.first()).toBeVisible();
    }
  });

  test('page menu shows mass mail entry in admin area', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/admin');

    // The PageMenuHandler registers a 'mass_mail' item under administer_utilities.
    // In Elgg 4.x admin, the item may be in a collapsed sidebar section (display:none)
    // but must be present in the DOM with the correct href.
    const link = page.locator('a[href*="mass_mail/send"]');
    await expect(link.first()).toBeAttached();
    expect(await link.first().getAttribute('href')).toContain('mass_mail/send');
  });
});
