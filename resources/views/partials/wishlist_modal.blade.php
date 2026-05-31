<div id="wishlistModal" class="wishlist-modal-overlay" style="display:none; opacity:0; transition: opacity 0.3s ease;">
    <div class="wishlist-modal-card">
        <div class="wishlist-modal-header">
            <h3><i class="fa-solid fa-heart" style="color:#f87171; margin-right:8px;"></i> Add to Wishlist</h3>
            <button onclick="closeWishlistModal()" class="wishlist-modal-close-btn">&times;</button>
        </div>
        
        <div class="wishlist-modal-body">
            <!-- Room Preview Header -->
            <div class="wishlist-room-preview" style="display:flex; gap:16px; margin-bottom:20px; background:rgba(255,255,255,0.03); padding:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.05);">
                <img id="wishlistPreviewImg" src="" alt="Room Preview" style="width:70px; height:70px; object-fit:cover; border-radius:8px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="display:flex; flex-direction:column; justify-content:center;">
                    <h4 id="wishlistPreviewTitle" style="margin:0; font-size:15px; color:#ffffff; font-weight:600; line-height:1.3;">Room Title</h4>
                    <p id="wishlistPreviewPrice" style="margin:4px 0 0 0; font-size:13px; color:#f87171; font-weight:700;">$0 / night</p>
                </div>
            </div>

            <!-- Existing collections / groups list -->
            <div class="wishlist-group-selector">
                <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600; color:#cbd5e1;">Select an Existing Collection</label>
                <div id="wishlistGroupsList" class="wishlist-groups-scroll-area">
                    <!-- Loaded dynamically via AJAX -->
                </div>
            </div>

            <!-- Create new group name -->
            <div class="wishlist-new-group-input" style="margin-top:20px;">
                <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600; color:#cbd5e1;">Or Create New Collection</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" id="newGroupNameInput" placeholder="e.g. Dream Summer Trip" class="wishlist-input-field">
                    <button onclick="saveWishlistWithNewGroup()" class="wishlist-create-btn">Create</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Wishlist Overlay and Card styling */
.wishlist-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(10, 10, 20, 0.75);
    backdrop-filter: blur(12px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}
.wishlist-modal-card {
    background: linear-gradient(135deg, #181829, #0f0f1c);
    border: 1px solid rgba(248, 113, 113, 0.2);
    border-radius: 20px;
    width: 90%;
    max-width: 420px;
    padding: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(248, 113, 113, 0.05);
    color: #f1f5f9;
    transform: scale(0.9);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.wishlist-modal-overlay.open {
    opacity: 1 !important;
}
.wishlist-modal-overlay.open .wishlist-modal-card {
    transform: scale(1);
}
.wishlist-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding-bottom: 12px;
}
.wishlist-modal-header h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    background: linear-gradient(to right, #ffffff, #e2e8f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.wishlist-modal-close-btn {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 24px;
    cursor: pointer;
    transition: color 0.2s;
}
.wishlist-modal-close-btn:hover {
    color: #ef4444;
}
.wishlist-groups-scroll-area {
    max-height: 150px;
    overflow-y: auto;
    background: rgba(0, 0, 0, 0.25);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.wishlist-groups-scroll-area::-webkit-scrollbar {
    width: 6px;
}
.wishlist-groups-scroll-area::-webkit-scrollbar-thumb {
    background: rgba(248, 113, 113, 0.3);
    border-radius: 10px;
}
.wishlist-group-item-btn {
    width: 100%;
    text-align: left;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 10px 14px;
    border-radius: 8px;
    color: #cbd5e1;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.wishlist-group-item-btn:hover {
    background: linear-gradient(135deg, rgba(248, 113, 113, 0.15), rgba(99, 102, 241, 0.15));
    border-color: rgba(248, 113, 113, 0.4);
    color: #ffffff;
    transform: translateY(-1px);
}
.wishlist-input-field {
    flex: 1;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 10px 14px;
    color: #ffffff;
    font-size: 13.5px;
    outline: none;
    transition: border-color 0.2s;
}
.wishlist-input-field:focus {
    border-color: #f87171;
}
.wishlist-create-btn {
    background: #f87171;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.2s;
}
.wishlist-create-btn:hover {
    background: #ef4444;
}
</style>
