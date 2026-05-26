import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                deep: "#070b12",
                surface: "#0c1220",
                base: "#090e1a",
                card: {
                    DEFAULT: "#0e1628",
                    hover: "#111c34",
                },
                field: "#111928",
                rim: {
                    DEFAULT: "#182235",
                    light: "#1e2d45",
                },
                accent: {
                    DEFAULT: "#1a6cf6",
                    bright: "#4d9bff",
                    cyan: "#00d4ff",
                    green: "#00e5a0",
                    orange: "#ff8c42",
                    purple: "#a78bfa",
                    red: "#ff4466",
                },
                ink: {
                    primary: "#dde9ff",
                    secondary: "#8ba5c8",
                    muted: "#4a6280",
                    dim: "#2d4260",
                },
            },

            fontFamily: {
                syne: ["Syne", "sans-serif"],
                space: ['"Space Mono"', "monospace"],
            },

            spacing: {
                sidebar: "240px",
                topbar: "60px",
            },
            width: { sidebar: "240px" },
            height: { topbar: "60px" },

            backgroundImage: {
                "grid-pattern": [
                    "linear-gradient(rgba(26,108,246,0.06) 1px, transparent 1px)",
                    "linear-gradient(90deg, rgba(26,108,246,0.06) 1px, transparent 1px)",
                ].join(", "),
                scanlines:
                    "repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.025) 2px, rgba(0,0,0,0.025) 4px)",
                "grad-accent": "linear-gradient(90deg, #1a6cf6, #1258d6)",
                "grad-accent-diag": "linear-gradient(135deg, #1a6cf6, #00d4ff)",
            },

            boxShadow: {
                accent: "0 3px 14px rgba(26,108,246,0.30)",
                "accent-lg": "0 5px 20px rgba(26,108,246,0.45)",
                brand: "0 0 14px rgba(26,108,246,0.35)",
                "glow-green": "0 0 6px #00e5a0",
                card: "0 40px 80px rgba(0,0,0,0.55)",
                login: "0 0 0 1px rgba(26,108,246,0.10), 0 40px 80px rgba(0,0,0,0.60), inset 0 1px 0 rgba(255,255,255,0.04)",
            },

            animation: {
                "grid-drift": "grid-drift 20s linear infinite",
                "orb-float": "orb-float 12s ease-in-out infinite alternate",
                "orb-float-rev":
                    "orb-float 16s ease-in-out infinite alternate-reverse",
                "orb-float-slow":
                    "orb-float 14s ease-in-out infinite alternate",
                "orb-float-slow-rev":
                    "orb-float 18s ease-in-out infinite alternate-reverse",
                "pulse-dot": "pulse-dot 2s ease-in-out infinite",
                "card-in": "card-in 0.7s cubic-bezier(0.22,1,0.36,1) both",
                "btn-shine": "btn-shine 0.5s",
            },

            keyframes: {
                "grid-drift": {
                    "0%": { backgroundPosition: "0 0" },
                    "100%": { backgroundPosition: "40px 40px" },
                },
                "orb-float": {
                    "0%": { transform: "translate(0,0)" },
                    "100%": { transform: "translate(30px,30px)" },
                },
                "pulse-dot": {
                    "0%, 100%": { opacity: "1" },
                    "50%": { opacity: "0.4" },
                },
                "card-in": {
                    from: {
                        opacity: "0",
                        transform: "translateY(24px) scale(0.97)",
                    },
                    to: { opacity: "1", transform: "translateY(0) scale(1)" },
                },
                "btn-shine": {
                    from: { transform: "translateX(-100%)" },
                    to: { transform: "translateX(100%)" },
                },
            },
        },
    },
    plugins: [],
};
