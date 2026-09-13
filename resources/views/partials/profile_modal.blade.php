<!-- Floating Profile Modal -->
<div id="profile-floating-modal" class="profile-modal-overlay" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; opacity: 0; transition: opacity 0.25s ease;">
    <div class="profile-modal-card" style="background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); border-radius: 16px; width: 100%; max-width: 540px; overflow: hidden; transform: scale(0.95) translateY(12px); transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1); display: flex; flex-direction: column;">
        
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding: 18px 24px; background: var(--bg-surface);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(37, 99, 235, 0.12); border: 1px solid rgba(37, 99, 235, 0.25); display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin: 0;">Profil Saya</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0;">Pengaturan informasi & akun Anda</p>
                </div>
            </div>
            <button type="button" id="profile-floating-close" onclick="closeProfileModal()" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 8px; transition: all 0.2s ease;" title="Tutup Modal" onmouseover="this.style.backgroundColor='var(--bg-surface2)'; this.style.color='var(--text-primary)';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--text-muted)';">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content Body -->
        <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px; max-height: calc(90vh - 80px); overflow-y: auto;">
            
            <!-- User Avatar & Badge Card -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 16px; background: var(--bg-surface2); border: 1px solid var(--border); border-radius: 12px; text-align: center;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%); border: 2px solid rgba(37, 99, 235, 0.3); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.6rem; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0;">{{ auth()->user()->name }}</h4>
                    <span class="badge-custom badge-blue" style="font-size: 0.7rem; padding: 4px 10px; letter-spacing: 0.04em;">
                        {{ str_replace('_', ' ', strtoupper(auth()->user()->role)) }}
                    </span>
                </div>
            </div>

            <!-- Profile Edit Form -->
            <form action="{{ route('profile.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div class="form-group-custom">
                    <label for="profile-input-name" class="form-label-custom" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-faint);">Nama Lengkap</label>
                    <input type="text" id="profile-input-name" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-input-custom" style="padding: 11px 14px; font-weight: 600;" required>
                    @error('name')
                        <span class="form-error-custom" style="color: #ef4444; font-size: 0.78rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-custom">
                    <label for="profile-input-email" class="form-label-custom" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-faint);">Alamat Email</label>
                    <input type="email" id="profile-input-email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-input-custom" style="padding: 11px 14px; font-weight: 600;" required>
                    @error('email')
                        <span class="form-error-custom" style="color: #ef4444; font-size: 0.78rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-faint);">Program Studi</label>
                    <div class="form-input-custom" style="background-color: var(--bg-surface2); color: var(--text-muted); font-weight: 600; padding: 11px 14px; cursor: not-allowed; opacity: 0.85;">
                        {{ auth()->user()->prodi?->nama_prodi ?? 'Umum / P2MP' }}
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 10px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <button type="button" onclick="closeProfileModal()" class="btn btn-secondary" style="padding: 10px 18px; font-size: 0.85rem;">
                        Batal
                    </button>
                    <button type="submit" id="profile-form-submit" class="btn btn-primary" style="padding: 10px 22px; font-size: 0.85rem; font-weight: 600;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
.profile-modal-overlay.show {
    opacity: 1 !important;
}
.profile-modal-overlay.show .profile-modal-card {
    transform: scale(1) translateY(0) !important;
}
</style>

<script>
function openProfileModal() {
    const modal = document.getElementById('profile-floating-modal');
    const profileMenu = document.getElementById('profile-menu');
    if (profileMenu) profileMenu.style.display = 'none';
    if (modal) {
        modal.style.display = 'flex';
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
        document.body.style.overflow = 'hidden';
    }
}

function closeProfileModal() {
    const modal = document.getElementById('profile-floating-modal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 250);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('profile-floating-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeProfileModal();
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('profile-floating-modal');
            if (modal && modal.classList.contains('show')) {
                closeProfileModal();
            }
        }
    });

    @if($errors->has('name') || $errors->has('email') || session('open_profile_modal') || request('open_profile'))
        openProfileModal();
    @endif
});
</script>
