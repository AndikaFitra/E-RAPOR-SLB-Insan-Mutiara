import { test, expect } from '@playwright/test';
async function login(page, username) {
    await page.goto('/login');
    await page.locator('[name=username]').fill(username);
    await page.locator('[name=password]').fill('password');
    await page.locator('button[type=submit]').click();
    await expect(page).toHaveURL(/dashboard/);
}
test('admin navigation works on desktop and mobile', async ({ page }) => {
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await login(page, 'admin');
    for (const url of ['/admin/siswa','/admin/penugasan','/admin/ekskul-penugasan','/admin/sekolah']) {
        const response = await page.goto(url);
        expect(response.status()).toBe(200);
    }
    await page.goto('/admin/penugasan');
    await expect(page.getByRole('link',{name:'Kembali ke Kelas'})).toHaveAttribute('href', /\/admin\/kelas$/);
    await page.screenshot({ path:'storage/framework/testing/admin-desktop.png',fullPage:true });
    await page.setViewportSize({ width:390,height:844 });
    await page.goto('/admin/penugasan');
    const menu=page.getByRole('button',{name:'Buka menu',exact:true});
    await expect(menu).toBeVisible();
    await menu.click();
    await expect(page.locator('body')).toHaveClass(/menu-open/);
    await expect(page.locator('aside').first()).toBeVisible();
    await menu.click();
    await expect(page.locator('aside').first()).toBeHidden();
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth + 1)).toBeTruthy();
    await page.screenshot({ path:'storage/framework/testing/admin-mobile.png',fullPage:true });
    expect(errors).toEqual([]);
});

test('account table keeps actions aligned and opens delete confirmation', async ({page}) => {
    await login(page, 'admin');
    await page.setViewportSize({width:1600,height:900});
    await page.goto('/admin/akun');
    const row = page.locator('table tbody tr', {hasText:'Ahmad Fauzi'});
    await expect(row.getByRole('link',{name:'Edit akun'})).toBeVisible();
    await expect(row.getByRole('link',{name:'Password'})).toBeVisible();
    await expect(row.getByRole('button',{name:'Nonaktifkan'})).toBeVisible();
    await row.getByRole('button',{name:'Hapus'}).click();
    await expect(page.getByText('Hapus Akun Pengguna')).toBeVisible();
    await expect(page.locator('p[x-text="itemName"]')).toHaveText('ahmadfauzi@slbinsanmutiara.sch.id');
    await page.getByRole('button',{name:'Batal'}).click();
    await expect(page.getByText('Hapus Akun Pengguna')).toBeHidden();
    await page.screenshot({path:'storage/framework/testing/akun-desktop.png',fullPage:true});
    await page.setViewportSize({width:390,height:844});
    await page.goto('/admin/akun');
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth + 1)).toBeTruthy();
    await page.screenshot({path:'storage/framework/testing/akun-mobile.png',fullPage:true});
});
test('archive table shows report links and stays within mobile viewport', async ({page}) => {
    await login(page, 'admin');
    await page.setViewportSize({width:1600,height:900});
    await page.goto('/admin/arsip');
    await expect(page.locator('.archive-table tbody tr').first()).toBeVisible();
    await expect(page.locator('.archive-table').getByRole('link',{name:'Detail semester'}).first()).toHaveAttribute('href', /reports|dokumen-rapor|rapor/);
    await expect(page.locator('.archive-table').getByRole('link',{name:'PDF'}).first()).toHaveAttribute('href', /pdf=1/);
    await page.screenshot({path:'storage/framework/testing/arsip-desktop.png',fullPage:true});
    await page.setViewportSize({width:390,height:844});
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth + 1)).toBeTruthy();
    await page.screenshot({path:'storage/framework/testing/arsip-mobile.png',fullPage:true});
});
test('student table uses matching level and phase labels', async ({page}) => {
    await login(page, 'admin');
    await page.setViewportSize({width:1600,height:900});
    await page.goto('/admin/siswa');
    const table = page.locator('table').first();
    await expect(table.getByRole('columnheader',{name:'Tingkat / Fase'})).toBeVisible();
    await expect(table.locator('tbody tr').first()).toContainText(/\b(?:VII|VIII|IX|X|XI|XII)\s*\/\s*[A-F]\b/);
    await page.screenshot({path:'storage/framework/testing/siswa-table-desktop.png',fullPage:true});
    await page.setViewportSize({width:390,height:844});
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth + 1)).toBeTruthy();
    await page.screenshot({path:'storage/framework/testing/siswa-table-mobile.png',fullPage:true});
});
test('admin can confirm student deactivation from the table', async ({page}) => {
    await login(page, 'admin');
    await page.goto('/admin/siswa');
    const row = page.locator('table tbody tr', {hasText:'Alya Putri'});
    await row.getByRole('button',{name:'Nonaktifkan'}).click();
    await expect(page.getByText('Nonaktifkan siswa?')).toBeVisible();
    await expect(page.locator('p[x-text="itemName"]')).toHaveText('Alya Putri');
    await page.locator('input[type="checkbox"][x-model="confirmed"]').check();
    await page.getByRole('button',{name:'Nonaktifkan siswa'}).click();
    await expect(page.locator('table tbody tr', {hasText:'Alya Putri'})).toContainText('Tidak Aktif');
    await expect(page.locator('table tbody tr', {hasText:'Alya Putri'}).getByText('Sudah nonaktif')).toBeVisible();
});
test('teacher delete opens confirmation and shows assignment restriction', async ({page}) => {
    await login(page, 'admin');
    await page.goto('/admin/guru');
    const row = page.locator('table tbody tr', {hasText:'Ahmad Fauzi'});
    await row.getByRole('button',{name:'Hapus'}).click();
    await expect(page.getByText('Hapus data guru?')).toBeVisible();
    await expect(page.locator('p[x-text="itemName"]')).toContainText('Ahmad Fauzi');
    await page.locator('input[type="checkbox"][x-model="confirmed"]').check();
    await page.getByRole('button',{name:'Hapus data'}).click();
    await expect(page.getByRole('alert')).toContainText('Guru tidak dapat dihapus karena masih terkait akun atau penugasan.');
    await expect(page.locator('table tbody tr', {hasText:'Ahmad Fauzi'})).toBeVisible();
});
test('wali can save class scores and cancel leaving unsaved changes', async ({ page }) => {
    const errors=[];
    page.on('pageerror',e=>errors.push(e.message));
    await login(page,'rinapratiwi');
    await page.goto('/wali-kelas/nilai-asesmen');
    await expect(page.getByRole('link',{name:'Kembali ke Beranda'})).toBeVisible();
    await page.getByRole('link',{name:'Input / edit nilai'}).first().click();
    const score=page.locator('input[type=number]:enabled').first();
    await score.fill('42');
    await page.locator('textarea:enabled').first().fill('Capaian hasil pengujian browser.');
    page.once('dialog',d=>d.dismiss());
    await page.getByRole('link',{name:'Kembali',exact:true}).click();
    await expect(score).toHaveValue('42');
    await page.getByRole('button',{name:'Simpan nilai kelas'}).click();
    await expect(page.locator('input[type=number]:enabled').first()).toHaveValue('42');
    await page.reload();
    await expect(page.locator('input[type=number]:enabled').first()).toHaveValue('42');
    await page.setViewportSize({width:390,height:844});
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth + 1)).toBeTruthy();
    await page.screenshot({path:'storage/framework/testing/asesmen-mobile.png',fullPage:true});
    expect(errors).toEqual([]);
});
test('kepala can export the filtered ledger',async ({page})=>{
    await login(page,'kepsek');
    await page.goto('/kepala-sekolah/leger');
    await expect(page.getByRole('link',{name:'Kembali ke Beranda'})).toBeVisible();
    await expect(page.getByText('Kriteria ketuntasan belum ditetapkan oleh sekolah.',{exact:false})).toBeVisible();
    await page.locator('[name=maksimum]').fill('100');
    await page.getByRole('button',{name:'Terapkan filter'}).click();
    const pending=page.waitForEvent('download');
    await page.getByRole('link',{name:'Unduh Excel (.xlsx)'}).click();
    expect((await pending).suggestedFilename()).toBe('leger.xlsx');
    await page.screenshot({path:'storage/framework/testing/leger-desktop.png',fullPage:true});
});

test('preview uses the same report layout and student data', async ({page}) => {
    await login(page, 'rinapratiwi');
    await page.goto('/wali-kelas/rapor-saya');
    await page.getByRole('link', {name:'Preview / periksa kelengkapan'}).first().click();
    const preview = await page.getByRole('link', {name:'Pratinjau cetak'}).getAttribute('href');
    await page.goto(preview);
    await expect(page.getByText('HASIL BELAJAR PESERTA DIDIK')).toBeVisible();
    await expect(page.getByText('KETERANGAN DIRI PESERTA DIDIK')).toBeVisible();
    await expect(page.getByText('Muatan Pelajaran')).toBeVisible();
    await expect(page.getByText('Keputusan:')).toBeVisible();
    await page.screenshot({path:'storage/framework/testing/report-preview-browser.png',fullPage:true});
});

test('principal approval uses themed dialog and equal action buttons', async ({page}) => {
    await login(page, 'kepsek');
    await page.goto('/kepala-sekolah/persetujuan');
    await page.locator('tr', {hasText:'Elsa Rahma'}).getByRole('link', {name:'Review detail'}).click();
    await expect(page.getByRole('button', {name:'Setujui rapor',exact:true})).toBeDisabled();
    await page.goto('/kepala-sekolah/persetujuan');
    const row = page.locator('tr', {hasText:'Bima Pratama'});
    await row.getByRole('link', {name:'Review detail'}).click();
    const preview = page.getByRole('link', {name:'Pratinjau cetak'});
    const download = page.getByRole('link', {name:'Unduh PDF'});
    const approve = page.getByRole('button', {name:'Setujui rapor',exact:true});
    await expect(approve).toBeEnabled();
    const heights = await Promise.all([preview,download,approve].map(async item => (await item.boundingBox()).height));
    expect(new Set(heights).size).toBe(1);
    let nativeDialogs = 0;
    page.on('dialog', () => nativeDialogs++);
    await approve.click();
    const dialog = page.getByRole('dialog', {name:'Setujui rapor ini?'});
    await expect(dialog).toBeVisible();
    expect(nativeDialogs).toBe(0);
    await dialog.getByRole('button', {name:'Batal'}).click();
    await expect(dialog).toBeHidden();
    await approve.click();
    await expect(dialog).toBeVisible();
    await expect(dialog).toHaveCSS('opacity', '1');
    await page.screenshot({path:'storage/framework/testing/persetujuan-modal.png'});
    await dialog.getByRole('button', {name:'Ya, setujui rapor'}).click();
    await expect(page.getByText('Rapor disetujui. PDF final dan tautan penerima sudah dibuat.')).toBeVisible();
});
