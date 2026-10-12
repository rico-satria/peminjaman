function filterRows(query) {
  const normalizedQuery = query.trim().toLocaleLowerCase("id-ID");

  document.querySelectorAll("#filterable .catalog-card").forEach((card) => {
    const isMatch = card.innerText
      .toLocaleLowerCase("id-ID")
      .includes(normalizedQuery);
    card.hidden = !isMatch;
  });
}

const sidebar = document.getElementById("sidebar");
const menuToggle = document.querySelector(".menu-toggle");

if (sidebar && menuToggle) {
  const scrim = document.createElement("button");
  scrim.type = "button";
  scrim.className = "sidebar-scrim";
  scrim.setAttribute("aria-label", "Tutup menu navigasi");
  document.body.appendChild(scrim);

  const setSidebarOpen = (isOpen) => {
    sidebar.classList.toggle("open", isOpen);
    scrim.classList.toggle("open", isOpen);
    menuToggle.setAttribute("aria-expanded", String(isOpen));
  };

  menuToggle.setAttribute("aria-label", "Buka menu navigasi");
  menuToggle.setAttribute("aria-controls", "sidebar");
  menuToggle.setAttribute("aria-expanded", "false");
  menuToggle.removeAttribute("onclick");
  menuToggle.addEventListener("click", () =>
    setSidebarOpen(!sidebar.classList.contains("open")),
  );
  scrim.addEventListener("click", () => setSidebarOpen(false));
  sidebar
    .querySelectorAll("a")
    .forEach((link) =>
      link.addEventListener("click", () => setSidebarOpen(false)),
    );

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") setSidebarOpen(false);
  });
}

const profile = document.querySelector(".profile");

if (profile) {
  const profileMenu = profile.querySelector(".profile-menu");

  profile.setAttribute("role", "button");
  profile.setAttribute("tabindex", "0");
  profile.setAttribute("aria-haspopup", "menu");
  profile.setAttribute("aria-expanded", "false");

  const setProfileOpen = (isOpen) => {
    profile.classList.toggle("open", isOpen);
    profile.setAttribute("aria-expanded", String(isOpen));
  };

  profile.addEventListener("click", (event) => {
    if (event.target.closest(".profile-menu")) return;
    event.stopPropagation();
    setProfileOpen(!profile.classList.contains("open"));
  });

  profile.addEventListener("keydown", (event) => {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      setProfileOpen(!profile.classList.contains("open"));
    }
    if (event.key === "Escape") {
      setProfileOpen(false);
      profile.focus();
    }
  });

  if (profileMenu) {
    profileMenu.setAttribute("role", "menu");
    profileMenu.querySelectorAll("a").forEach((link) => {
      link.setAttribute("role", "menuitem");
    });
  }

  document.addEventListener("click", (event) => {
    if (!profile.contains(event.target)) setProfileOpen(false);
  });
}
