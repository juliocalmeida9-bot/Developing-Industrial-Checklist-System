"""
views/login_view.py — Tela de autenticação do CheckInd Mobile.
Design industrial: fundo escuro, acentos âmbar/laranja.
"""

import flet as ft
from config import COLORS


def login_view(page: ft.Page, app_state: dict, on_success):
    """
    Renderiza a tela de login.

    Args:
        page: instância ft.Page do Flet.
        app_state: dict global com 'api_client' e 'employee'.
        on_success: callback(employee: dict) chamado após login bem-sucedido.
    """
    page.bgcolor = COLORS["bg_dark"]
    page.scroll = None

    # ── Campos de formulário ──────────────────────────────────────────
    email_field = ft.TextField(
        label="E-mail ou Matrícula",
        bgcolor=COLORS["bg_input"],
        color=COLORS["text"],
        border_color=COLORS["primary"],
        focused_border_color=COLORS["accent"],
        label_style=ft.TextStyle(color=COLORS["text_muted"]),
        cursor_color=COLORS["accent"],
        prefix_icon=ft.Icons.PERSON_OUTLINE,
        width=340,
    )

    password_field = ft.TextField(
        label="Senha",
        password=True,
        can_reveal_password=True,
        bgcolor=COLORS["bg_input"],
        color=COLORS["text"],
        border_color=COLORS["primary"],
        focused_border_color=COLORS["accent"],
        label_style=ft.TextStyle(color=COLORS["text_muted"]),
        cursor_color=COLORS["accent"],
        prefix_icon=ft.Icons.LOCK_OUTLINE,
        width=340,
    )

    error_text = ft.Text(
        "",
        color=COLORS["danger"],
        size=13,
        text_align=ft.TextAlign.CENTER,
        visible=False,
    )

    loading = ft.ProgressRing(
        color=COLORS["accent"],
        width=28,
        height=28,
        stroke_width=3,
        visible=False,
    )

    login_btn = ft.ElevatedButton(
        text="ENTRAR",
        icon=ft.Icons.LOGIN,
        icon_color="#0f172a",
        bgcolor=COLORS["accent"],
        color="#0f172a",
        width=340,
        height=50,
        style=ft.ButtonStyle(
            shape=ft.RoundedRectangleBorder(radius=10),
            text_style=ft.TextStyle(
                weight=ft.FontWeight.BOLD,
                size=16,
                letter_spacing=1.5,
            ),
        ),
    )

    # ── Lógica de login ───────────────────────────────────────────────
    def set_loading(state: bool):
        loading.visible = state
        login_btn.disabled = state
        email_field.disabled = state
        password_field.disabled = state
        page.update()

    def show_error(msg: str):
        error_text.value = msg
        error_text.visible = True
        page.update()

    def do_login(e):
        # Limpar erro anterior
        error_text.visible = False
        page.update()

        identifier = email_field.value.strip() if email_field.value else ""
        password = password_field.value if password_field.value else ""

        if not identifier:
            show_error("⚠ Informe o e-mail ou matrícula.")
            return
        if not password:
            show_error("⚠ Informe a senha.")
            return

        set_loading(True)

        api = app_state["api_client"]
        result = api.login(identifier, password)

        set_loading(False)

        if "error" in result:
            show_error(f"✖ {result['error']}")
            return

        # A API pode retornar diferentes estruturas — tenta as mais comuns
        employee = (
            result.get("employee")
            or result.get("user")
            or result.get("data")
        )

        if not employee and "token" in result:
            # Fallback: API retornou token mas sem objeto employee explícito
            employee = {
                "name": result.get("name", identifier),
                "matricula": result.get("matricula", ""),
                "sector": result.get("sector", ""),
            }

        if not employee:
            show_error("✖ Resposta inesperada do servidor. Tente novamente.")
            return

        on_success(employee)

    login_btn.on_click = do_login

    # Permitir login com Enter
    def on_key(e: ft.KeyboardEvent):
        if e.key == "Enter":
            do_login(None)

    page.on_keyboard_event = on_key

    # ── Layout ────────────────────────────────────────────────────────
    card = ft.Container(
        content=ft.Column(
            controls=[
                # Logo / ícone
                ft.Container(
                    content=ft.Column(
                        [
                            ft.Icon(
                                ft.Icons.SETTINGS,
                                color=COLORS["accent"],
                                size=52,
                            ),
                            ft.Text(
                                "CheckInd",
                                size=30,
                                weight=ft.FontWeight.BOLD,
                                color=COLORS["accent"],
                                text_align=ft.TextAlign.CENTER,
                            ),
                            ft.Text(
                                "TechForge Industrial",
                                size=13,
                                color=COLORS["text_muted"],
                                text_align=ft.TextAlign.CENTER,
                            ),
                        ],
                        horizontal_alignment=ft.CrossAxisAlignment.CENTER,
                        spacing=4,
                    ),
                    padding=ft.padding.only(bottom=10),
                ),

                ft.Divider(color=COLORS["bg_input"], thickness=1),

                ft.Container(height=4),

                email_field,
                password_field,

                ft.Container(
                    content=error_text,
                    width=340,
                    alignment=ft.alignment.center,
                ),

                ft.Container(height=4),

                ft.Row(
                    [loading, login_btn],
                    alignment=ft.MainAxisAlignment.CENTER,
                    spacing=12,
                ),

                ft.Container(height=6),

                ft.Text(
                    "Sistema de Controle de Manutenção · v1.0",
                    size=11,
                    color=COLORS["text_muted"],
                    text_align=ft.TextAlign.CENTER,
                    opacity=0.6,
                ),
            ],
            horizontal_alignment=ft.CrossAxisAlignment.CENTER,
            spacing=14,
        ),
        width=420,
        padding=ft.padding.symmetric(horizontal=40, vertical=36),
        bgcolor=COLORS["bg_card"],
        border_radius=20,
        shadow=ft.BoxShadow(
            spread_radius=0,
            blur_radius=30,
            color=ft.Colors.with_opacity(0.4, "#000000"),
            offset=ft.Offset(0, 8),
        ),
    )

    page.controls.clear()
    page.controls.append(
        ft.Container(
            content=ft.Column(
                [
                    # Barra superior decorativa
                    ft.Container(
                        content=ft.Row(
                            [
                                ft.Icon(ft.Icons.FACTORY, color=COLORS["accent"], size=16),
                                ft.Text(
                                    "SISTEMA INDUSTRIAL",
                                    size=11,
                                    color=COLORS["accent"],
                                    weight=ft.FontWeight.BOLD,
                                    letter_spacing=2,
                                ),
                            ],
                            alignment=ft.MainAxisAlignment.CENTER,
                            spacing=8,
                        ),
                        padding=ft.padding.symmetric(vertical=10),
                    ),
                    card,
                ],
                horizontal_alignment=ft.CrossAxisAlignment.CENTER,
                alignment=ft.MainAxisAlignment.CENTER,
                spacing=20,
                expand=True,
            ),
            expand=True,
            alignment=ft.alignment.center,
            bgcolor=COLORS["bg_dark"],
        )
    )
    page.update()
