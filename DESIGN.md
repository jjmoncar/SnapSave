# Design System Strategy: The Hyper-Luxe Prism

## 1. Overview & Creative North Star
This design system is built upon the Creative North Star of **"The Hyper-Luxe Prism."** Unlike standard utility-first downloader sites that feel transactional and cluttered, this system treats digital content like high-end editorial matter. We are not just building a tool; we are building a "Capture Gallery."

The system breaks the "template" look through **intentional asymmetry** and **tonal depth**. We move away from the rigid, boxed-in web of the past and toward a fluid, glass-like interface where light and color (inspired by the vibrant energy of social media) feel like they are radiating from *within* the components rather than being painted on top. 

**Key Principles:**
- **Refraction over Reflection:** Use gradients as light sources to guide the eye.
- **Breathable Sophistication:** Use extreme whitespace to make the "vibrant" colors feel premium, not "loud."
- **Organic Geometry:** High corner radii (`xl` tokens) to mimic the soft, hand-held nature of mobile devices.

---

## 2. Colors & Surface Architecture

### The "No-Line" Rule
**Explicit Instruction:** You are prohibited from using 1px solid borders to define sections or containers. Boundaries must be defined solely through background color shifts.
- A card should sit as a `surface-container-high` (`#20201f`) against a `surface` (`#0e0e0e`) background. 
- Use the Spacing Scale to create "voids" that act as separators.

### Surface Hierarchy & Nesting
Treat the UI as a series of physical layers. Use the `surface-container` tiers to create "nested" depth:
1.  **Base Layer:** `surface` (`#0e0e0e`) - The infinite canvas.
2.  **Sectional Layer:** `surface-container-low` (`#131313`) - Large background areas.
3.  **Component Layer:** `surface-container-highest` (`#262626`) - Individual cards or input wrappers.
4.  **Interaction Layer:** `surface-bright` (`#2c2c2c`) - Elements that need to "pop" on hover.

### The "Glass & Gradient" Rule
To achieve a signature feel, floating elements (like the URL Input or Header) must use **Glassmorphism**:
- **Background:** `surface-container` at 60% opacity.
- **Effect:** `backdrop-blur: 20px`.
- **Gradients:** Use transitions from `primary` (`#ff8aa5`) to `secondary` (`#ffb147`) for CTAs. This creates "visual soul" that flat colors lack.

---

## 3. Typography: Editorial Utility

The typography strategy relies on the juxtaposition of **Plus Jakarta Sans** (Character) and **Inter** (Clarity).

- **Display & Headlines (Plus Jakarta Sans):** These are your "vibe" setters. Use `display-lg` for the hero value proposition. The tight tracking and organic curves of Plus Jakarta Sans signal "Modern/Premium."
- **Body & Labels (Inter):** Inter is used for all functional text. It is invisible, highly readable, and doesn't compete with the vibrant color palette.
- **Hierarchy Tip:** Use `on-surface-variant` (`#adaaaa`) for secondary body text to create a sophisticated, low-contrast hierarchy that reduces cognitive load.

---

## 4. Elevation & Depth

### Tonal Layering
Depth is achieved by "stacking" surface tiers. Place a `surface-container-lowest` card on a `surface-container-low` section to create a soft, natural lift without needing a shadow.

### Ambient Shadows
When a "floating" effect is required (e.g., a modal or a primary action button), use **Ambient Shadows**:
- **Blur:** 40px - 60px.
- **Opacity:** 4% - 8%.
- **Color:** Use a tinted version of `primary` (`#ff8aa5`) or `on-surface`. Never use pure `#000000` for shadows.

### The "Ghost Border" Fallback
If a container lacks contrast (e.g., in light mode versions), use a **Ghost Border**: 
- Token: `outline-variant` (`#484847`) at **15% opacity**. 
- Result: A whisper of a line that defines the edge without creating "visual noise."

---

## 5. Components

### The Hero Input (The "Prism" Field)
The URL input is the soul of this app.
- **Background:** `surface-container-highest` with 40% opacity.
- **Blur:** 12px.
- **Shape:** `xl` (3rem) corner radius.
- **State:** On focus, the `outline` should transition into a subtle `primary` gradient glow.

### Buttons
- **Primary:** Full `primary` to `secondary` gradient. Shape: `full` (pill). Shadow: Ambient `primary` tint.
- **Secondary:** Transparent background with a `ghost border`. Text color: `primary`.
- **Tertiary:** No background. `label-md` weight. Used for "Clear All" or "Terms."

### Chips (Media Formats)
Use `surface-container-high` for unselected states and `secondary-container` for selected states. 
- **Rule:** Forbid the use of divider lines between chips. Use `sm` spacing.

### Cards & Result Lists
- **Structure:** Forbid divider lines.
- **Separation:** Use `surface-container-low` for the list item background.
- **Roundedness:** `lg` (2rem) to create a friendly, modern aesthetic.

### The "Capture Tray" (Specialty Component)
A bottom-sheet or side-drawer where recently downloaded videos sit. 
- **Visuals:** Use `glassmorphism` with a `surface-container-low` base. This allows the vibrant hero background to peek through, maintaining the "Prism" effect.

---

## 6. Do’s and Don'ts

### Do:
- **Use Intentional Asymmetry:** Align your hero text to the left but place the "Download" button slightly offset to the right in a "floating" container.
- **Prioritize Breathing Room:** If you think there is enough padding, add 1rem more. High-end design thrives on whitespace.
- **Animate the Glass:** When a user hovers over a glass card, increase the `backdrop-blur` and the `surface-tint` slightly.

### Don’t:
- **No Harsh Contrast:** Avoid pure white text on pure black backgrounds. Use `on-surface` on `surface`.
- **No Default Grids:** Don’t just place items in 3-column rows. Vary the widths of your cards (e.g., 60% width for a "Featured" download, 40% for "Instructions").
- **No Generic Icons:** Use minimalist, thin-stroke icons (1.5px weight) to match the Inter typography weight. Never use filled/chunky icons.