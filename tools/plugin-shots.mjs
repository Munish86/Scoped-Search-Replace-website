// Real screenshots of the Scoped Search & Replace plugin, taken on the demo site (http://localhost/srdemo).
// Usage: tools/wp-demo.sh eval-file tools/demo-content.php --user=demo && node tools/plugin-shots.mjs
//        then: php tools/make-shots.php      (PNG -> WebP in the theme's assets/images)
// Saves 2x PNGs in tools/.shots/plugin/ and the text of the replace confirmation in confirm.txt.
// The replace step changes the demo content; run demo-content.php again before the next run.
import fs from "node:fs";
import path from "node:path";
import { createRequire } from "node:module";
import { fileURLToPath } from "node:url";

const here = path.dirname(fileURLToPath(import.meta.url));
const base = ["../../restock-app", "../../shopify-bulk-uploader-app"].map((p) => path.resolve(here, p)).find((p) => fs.existsSync(path.join(p, "node_modules/playwright-core")));
const { chromium } = createRequire(path.join(base, "package.json"))("playwright-core");
const executablePath = [process.env.CHROME_PATH, "C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe", "C:/Program Files/Google/Chrome/Application/chrome.exe"].find((p) => p && fs.existsSync(p));

const site = process.env.DEMO || "http://localhost/srdemo";
const tool = `${site}/wp-admin/admin.php?page=scoped-search-replace`;
const OLD = "http://harbour-coffee.example";
const NEW = "https://www.harbour-coffee.example";
const out = path.join(here, ".shots/plugin");
fs.mkdirSync(out, { recursive: true });

const browser = await chromium.launch({ executablePath });
const page = await browser.newPage({ viewport: { width: 1180, height: 900 }, deviceScaleFactor: 2 });
page.setDefaultTimeout(120000);
await page.goto(`${site}/wp-login.php`);
await page.fill("#user_login", "demo");
await page.fill("#user_pass", "demo-local-only");
await Promise.all([page.waitForNavigation(), page.click("#wp-submit")]);

// Hide WordPress chrome that is not part of the plugin (update nags, footer text).
const tidy = () => page.addStyleTag({ content: "#wpadminbar, .update-nag, #wpfooter, .notice:not(.notice-success):not(.notice-info):not(.notice-error):not(.notice-warning) { display: none !important; } html.wp-toolbar { padding-top: 0 !important; }" });

async function setScope(form, { table, column, type, status, item }) {
  await form.locator('[name="selected_table"]').selectOption(table);
  await form.locator('[name="selected_column"]').selectOption(column);
  await form.locator('[name="selected_post_type"]').selectOption(type);
  await form.locator('[name="selected_status"]').selectOption(status);
  if (item) {
    const value = await form.locator('[name="selected_post_id"] option', { hasText: `${item} —` }).first().getAttribute("value");
    await form.locator('[name="selected_post_id"]').selectOption(value);
  }
}
const card = (n) => page.locator(".scopedsr-card").nth(n);
async function shot(locator, name, pad = 12) {
  await locator.scrollIntoViewIfNeeded();
  const b = await locator.boundingBox();
  await page.screenshot({ path: path.join(out, name), clip: { x: Math.max(0, b.x - pad), y: b.y - pad + (await page.evaluate(() => window.scrollY)), width: b.width + pad * 2, height: b.height + pad * 2 }, fullPage: true });
  console.log("saved", name);
}
// WordPress moves notices to the top of the page; put the result notice inside the results card for the shot.
async function noticeIntoResults() {
  await page.waitForLoadState("load");
  await page.waitForTimeout(800);
  await page.evaluate(() => {
    const n = document.querySelector(".notice-success, .notice-info");
    const cards = document.querySelectorAll(".scopedsr-card");
    const last = cards[cards.length - 1];
    if (n && last) { n.style.margin = "0 0 16px"; last.querySelector("h2").after(n); }
  });
}
async function union(locators, name, pad = 12) {
  const boxes = [];
  for (const l of locators) boxes.push(await l.boundingBox());
  const y0 = Math.min(...boxes.map((b) => b.y)), y1 = Math.max(...boxes.map((b) => b.y + b.height));
  const x0 = Math.min(...boxes.map((b) => b.x)), x1 = Math.max(...boxes.map((b) => b.x + b.width));
  const sy = await page.evaluate(() => window.scrollY);
  await page.screenshot({ path: path.join(out, name), clip: { x: x0 - pad, y: y0 - pad + sy, width: x1 - x0 + pad * 2, height: y1 - y0 + pad * 2 }, fullPage: true });
  console.log("saved", name);
}

// 1. The whole tool, empty (overview).
await page.goto(tool);
await tidy();
await shot(page.locator(".scopedsr-wrap"), "overview.png", 16);

// 2. Custom fields: every content type and status, so the serialized layout settings show up.
let form = card(0).locator("form");
await setScope(form, { table: "postmeta", column: "meta_value", type: "all", status: "all" });
await form.locator('[name="search_text"]').fill(OLD);
await Promise.all([page.waitForURL(/result=1/), form.locator('button[type="submit"]').click()]);
await tidy();
await shot(page.locator(".scopedsr-card").last(), "meta-results.png");

// 3. Pages, published only: search, then the results with Edit / View links.
await page.goto(tool);
await tidy();
form = card(0).locator("form");
await setScope(form, { table: "posts", column: "post_content", type: "page", status: "publish" });
await form.locator('[name="search_text"]').fill(OLD);
await Promise.all([page.waitForURL(/result=1/), form.locator('button[type="submit"]').click()]);
await tidy();
await shot(card(0), "search.png");
await noticeIntoResults();
await shot(page.locator(".scopedsr-card").last(), "results.png");

// 4. One page only: the scope box with "Wholesale" chosen.
form = card(1).locator("form");
await setScope(form, { table: "posts", column: "post_content", type: "page", status: "publish", item: "Wholesale" });
await shot(form.locator(".scopedsr-scope"), "scope-one-page.png");

// 5. Replace, in the same scope as the search (all published pages), with the confirmation accepted.
await setScope(form, { table: "posts", column: "post_content", type: "page", status: "publish" });
await form.locator('[name="selected_post_id"]').selectOption("0");
await form.locator('[name="old_text"]').fill(OLD);
await form.locator('[name="new_text"]').fill(NEW);
await shot(card(1), "replace.png");
page.once("dialog", async (d) => {
  fs.writeFileSync(path.join(out, "confirm.txt"), d.message());
  await d.accept();
});
await Promise.all([page.waitForURL(/result=1/), form.locator('button[type="submit"]').click()]);
await tidy();
await noticeIntoResults();
await shot(page.locator(".scopedsr-card").last(), "done.png");
console.log("notice:", await page.locator(".notice-success").first().innerText());

await browser.close();
