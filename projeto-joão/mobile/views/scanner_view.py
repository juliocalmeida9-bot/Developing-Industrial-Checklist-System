"""
views/scanner_view.py — Tela de escaneamento de QR Code ou entrada manual de token.
Usa cv2.QRCodeDetector() (opencv-python) em thread separada para câmera.
"""

import threading
import flet as ft
from config import COLORS


# ── Câmera / QR Code ─────────────────────────────────────────────────────────

def _scan_camera_thread(page: ft.Page, app_state: dict, on_scan_success, status_text: ft.Text, camera_btn: ft.ElevatedButton):
    """
    Executa em thread separada.
    Abre a câmera, detecta QR Code e, ao encontrar, dispara o processamento.
    """
    try:
        import cv2
    except ImportError:
        page.run_task(_update_status, page, status_text, camera_btn,
                      "✖ opencv-python não instalado. Use a entrada manual.", True)
        return

    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        page.run_task(_update_status, page, status_text, camera_btn,
                      "✖ Câmera não encontrada. Use a entrada manual.", True)
        return

    detector = cv2.QRCodeDetector()

    page.run_task(_update_status, page, status_text, camera_btn,
                  "📷 Câmera aberta — aponte para o QR Code (tecle Q para cancelar)", False)

    detected_token = None

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        data, bbox, _ = detector.detectAndDecode(frame)

        if data and data.strip():
            detected_token = data.strip()
            break

        cv2.imshow("CheckInd — Aponte para o QR Code  |  Q = cancelar", frame)
        if cv2.waitKey(1) & 0xFF == ord("q"):
            break

    cap.release()
    cv2.destroyAllWindows()

    if detected_token:
        page.run_task(_process_token, page, app_state, detected_token, on_scan_success, status_text, camera_btn)
    else:
        page.run_task(_update_status, page, status_text, camera_btn,
                      "Câmera fechada. Digite o token manualmente se necessário.", False)


async def _update_status(page: ft.Page, status_text: ft.Text, camera_btn: ft.ElevatedButton,
                         message: str, is_error: bool):
    status_text.value = message
    status_text.color = COLORS["danger"] if is_error else COLORS["text_muted"]
    status_text.visible = True
    camera_btn.disabled = False
    page.update()


async def _process_token(page: ft.Page, app_state: dict, token: str,
                         on_scan_success, status_text: ft.Text, camera_btn: ft.ElevatedButton):
    status_text.value = f"🔍 Consultando token: {token[:16]}…"
    status_text.color = COLORS["text_muted"]
    status_text.visible = True
    camera_btn.disabled = True
    page.update()

    api = app_state["api_client"]
    result = api.get_machine(token)

    camera_btn.disabled = False

    if "error" in result:
        status_text.value = f"✖ {result['error']}"
        status_text.color = COLORS["danger"]
        page.update()
        return

    if not result.get("machine"):
        status_text.value = "✖ Token inválido ou máquina não encontrada."
        status_text.color = COLORS["danger"]
        page.update()
        return

    status_text.visible = False
    page.update()
    on_scan_success(result)


# ── View principal ────────────────────────────────────────────────────────────

def scanner_view(page: ft.Page, app_state: dict, on_scan_success):
    """
    Renderiza a tela de escaneamento.

    Args:
        page: instância ft.Page do Flet.
        app_state: dict com 'api_client' e 'employee'.
        on_scan_success: callback(machine_data: dict) com dados da máquina.
    """
    page.bgcolor = COLORS["bg_dark"]
    page.scroll = ft.ScrollMode.AUTO

    employee = app_state.get("employee") or {}
    emp_name = employee.get("name", "Funcionário")
    emp_mat = employee.get("matricula", employee.get("registration", "—"))
    emp_sector = employee.get("sector", employee.get("department", "—"))

    # ── Widgets compartilhados ────────────────────────────────────────
    status_text = ft.Text(
        "",
        color=COLORS["text_muted"],
        size=13,
        text_align=ft.TextAlign.CENTER,
        visible=False,
    )

    loading_ring = ft.ProgressRing(
        color=COLORS["accent"],
        width=22,
        height=22,
        stroke_width=3,
        visible=False,
    )

    camera_btn = ft.ElevatedButton(
        text="  Abrir Câmera",
        icon=ft.Icons.CAMERA_ALT,
        icon_color="#0f172a",
        bgcolor=COLORS["primary"],
        color=COLORS["text"],
        height=48,
        style=ft.ButtonStyle(
            shape=ft.RoundedRectangleBorder(radius=10),
            text_style=ft.TextStyle(weight=ft.FontWeight.W_600, size=14),
        ),
    )

    token_field = ft.TextField(
        label="Token do QR Code",
        hint_text="Ex: ABC123XYZ",
        bgcolor=COLORS["bg_input"],
        color=COLORS["text"],
        border_color=COLORS["primary"],
        focused_border_color=COLORS["accent"],
        label_style=ft.TextStyle(color=COLORS["text_muted"]),
        cursor_color=COLORS["accent"],
        prefix_icon=ft.Icons.QR_CODE,
        expand=True,
    )

    consult_btn = ft.ElevatedButton(
        text="Consultar",
        icon=ft.Icons.SEARCH,
        icon_color="#0f172a",
        bgcolor=COLORS["accent"],
        color="#0f172a",
        height=48,
        style=ft.ButtonStyle(
            shape=ft.RoundedRectangleBorder(radius=10),
            text_style=ft.TextStyle(weight=ft.FontWeight.BOLD, size=14),
        ),
    )

    # ── Handlers ─────────────────────────────────────────────────────
    def open_camera(e):
        camera_btn.disabled = True
        status_text.value = "🔄 Iniciando câmera…"
        status_text.color = COLORS["text_muted"]
        status_text.visible = True
        page.update()

        threading.Thread(
            target=_scan_camera_thread,
            args=(page, app_state, on_scan_success, status_text, camera_btn),
            daemon=True,
        ).start()

    camera_btn.on_click = open_camera

    def consult_manual(e):
        token = token_field.value.strip() if token_field.value else ""
        if not token:
            status_text.value = "⚠ Digite o token antes de consultar."
            status_text.color = COLORS["danger"]
            status_text.visible = True
            page.update()
            return

        status_text.value = f"🔍 Consultando token: {token[:16]}…"
        status_text.color = COLORS["text_muted"]
        status_text.visible = True
        consult_btn.disabled = True
        loading_ring.visible = True
        page.update()

        def _do_request():
            api = app_state["api_client"]
            result = api.get_machine(token)

            consult_btn.disabled = False
            loading_ring.visible = False

            if "error" in result:
                status_text.value = f"✖ {result['error']}"
                status_text.color = COLORS["danger"]
                page.update()
                return

            if not result.get("machine"):
                status_text.value = "✖ Token inválido ou máquina não encontrada."
                status_text.color = COLORS["danger"]
                page.update()
                return

            status_text.visible = False
            page.update()
            on_scan_success(result)

        threading.Thread(target=_do_request, daemon=True).start()

    consult_btn.on_click = consult_manual

    def do_logout(e):
        api = app_state["api_client"]
        threading.Thread(target=api.logout, daemon=True).start()
        from views.login_view import login_view

        def _go():
            app_state["employee"] = None
            app_state["api_client"].token = None
            login_view(page, app_state, lambda emp: _on_login(emp))

        def _on_login(emp):
            app_state["employee"] = emp
            scanner_view(page, app_state, on_scan_success)

        _go()

    # ── Cards de seção ────────────────────────────────────────────────
    def _section_card(title: str, icon, content: ft.Control) -> ft.Container:
        return ft.Container(
            content=ft.Column(
                [
                    ft.Row(
                        [
                            ft.Icon(icon, color=COLORS["accent"], size=18),
                            ft.Text(
                                title,
                                size=14,
                                weight=ft.FontWeight.BOLD,
                                color=COLORS["accent"],
                                letter_spacing=0.5,
                            ),
                        ],
                        spacing=8,
                    ),
                    ft.Divider(color=COLORS["bg_input"], thickness=1),
                    content,
                ],
                spacing=12,
            ),
            bgcolor=COLORS["bg_card"],
            border_radius=14,
            padding=20,
            shadow=ft.BoxShadow(
                blur_radius=12,
                color=ft.Colors.with_opacity(0.3, "#000000"),
                offset=ft.Offset(0, 4),
            ),
        )

    # Card: info do funcionário
    employee_card = _section_card(
        "Funcionário Autenticado",
        ft.Icons.BADGE,
        ft.Column(
            [
                ft.Row(
                    [
                        ft.Icon(ft.Icons.PERSON, color=COLORS["text_muted"], size=16),
                        ft.Text(emp_name, color=COLORS["text"], size=15, weight=ft.FontWeight.W_500),
                    ],
                    spacing=8,
                ),
                ft.Row(
                    [
                        ft.Icon(ft.Icons.NUMBERS, color=COLORS["text_muted"], size=16),
                        ft.Text(f"Matrícula: {emp_mat}", color=COLORS["text_muted"], size=13),
                    ],
                    spacing=8,
                ),
                ft.Row(
                    [
                        ft.Icon(ft.Icons.FACTORY, color=COLORS["text_muted"], size=16),
                        ft.Text(f"Setor: {emp_sector}", color=COLORS["text_muted"], size=13),
                    ],
                    spacing=8,
                ),
            ],
            spacing=8,
        ),
    )

    # Card: câmera
    camera_card = _section_card(
        "Opção 1 — Câmera QR Code",
        ft.Icons.QR_CODE_SCANNER,
        ft.Column(
            [
                ft.Text(
                    "Aponte a câmera para o QR Code fixado na máquina.",
                    color=COLORS["text_muted"],
                    size=13,
                ),
                camera_btn,
            ],
            spacing=10,
        ),
    )

    # Card: token manual
    manual_card = _section_card(
        "Opção 2 — Token Manual",
        ft.Icons.KEYBOARD,
        ft.Column(
            [
                ft.Text(
                    "Digite o token impresso na etiqueta da máquina.",
                    color=COLORS["text_muted"],
                    size=13,
                ),
                ft.Row([token_field, consult_btn], spacing=10),
                ft.Row([loading_ring], alignment=ft.MainAxisAlignment.CENTER),
            ],
            spacing=10,
        ),
    )

    # ── Layout final ──────────────────────────────────────────────────
    page.controls.clear()
    page.controls.append(
        ft.Container(
            content=ft.Column(
                [
                    # AppBar inline
                    ft.Container(
                        content=ft.Row(
                            [
                                ft.Row(
                                    [
                                        ft.Icon(ft.Icons.SETTINGS, color=COLORS["accent"], size=20),
                                        ft.Text(
                                            "CheckInd",
                                            size=18,
                                            weight=ft.FontWeight.BOLD,
                                            color=COLORS["accent"],
                                        ),
                                    ],
                                    spacing=8,
                                ),
                                ft.ElevatedButton(
                                    text="Sair",
                                    icon=ft.Icons.LOGOUT,
                                    bgcolor=ft.Colors.with_opacity(0.15, COLORS["danger"]),
                                    color=COLORS["danger"],
                                    height=36,
                                    on_click=do_logout,
                                    style=ft.ButtonStyle(
                                        shape=ft.RoundedRectangleBorder(radius=8),
                                    ),
                                ),
                            ],
                            alignment=ft.MainAxisAlignment.SPACE_BETWEEN,
                        ),
                        bgcolor=COLORS["bg_card"],
                        padding=ft.padding.symmetric(horizontal=20, vertical=12),
                        border_radius=ft.border_radius.only(bottom_left=0, bottom_right=0),
                    ),

                    # Título da tela
                    ft.Container(
                        content=ft.Column(
                            [
                                ft.Text(
                                    "Escanear Máquina",
                                    size=22,
                                    weight=ft.FontWeight.BOLD,
                                    color=COLORS["text"],
                                ),
                                ft.Text(
                                    "Escaneie o QR Code ou insira o token manualmente",
                                    size=13,
                                    color=COLORS["text_muted"],
                                ),
                            ],
                            spacing=4,
                        ),
                        padding=ft.padding.symmetric(horizontal=20, vertical=14),
                    ),

                    # Cards empilhados
                    ft.Container(
                        content=ft.Column(
                            [
                                employee_card,
                                camera_card,
                                manual_card,
                                ft.Container(
                                    content=status_text,
                                    alignment=ft.alignment.center,
                                ),
                            ],
                            spacing=16,
                        ),
                        padding=ft.padding.symmetric(horizontal=16),
                    ),
                ],
                spacing=0,
                scroll=ft.ScrollMode.AUTO,
            ),
            bgcolor=COLORS["bg_dark"],
            expand=True,
        )
    )
    page.update()
