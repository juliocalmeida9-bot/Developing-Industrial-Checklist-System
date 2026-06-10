"""
mobile/main.py — Ponto de entrada do aplicativo CheckInd Mobile (Flet).
Gerencia a navegação entre as telas e o estado global da aplicação.
"""

import flet as ft
from config import API_BASE_URL, COLORS
from api.client import ApiClient
from views.login_view import login_view
from views.scanner_view import scanner_view
from views.checklist_view import checklist_view
from views.success_view import success_view


def main(page: ft.Page):
    # Configurações básicas da janela (caso rodando em desktop/teste)
    page.title = "CheckInd Mobile"
    page.bgcolor = COLORS["bg_dark"]
    
    # Tratamento de erro compatível com múltiplas versões do Flet para tamanho da janela
    try:
        page.window.width = 400
        page.window.height = 820
        page.window.resizable = False
    except AttributeError:
        try:
            page.window_width = 400
            page.window_height = 820
        except Exception:
            pass

    # Estado global do aplicativo
    app_state = {
        "api_client": ApiClient(API_BASE_URL),
        "employee": None,
    }

    # ── Fluxo de Navegação por Callbacks ──────────────────────────────

    def show_login_screen():
        login_view(
            page=page,
            app_state=app_state,
            on_success=on_login_success
        )

    def on_login_success(employee: dict):
        app_state["employee"] = employee
        show_scanner_screen()

    def show_scanner_screen():
        scanner_view(
            page=page,
            app_state=app_state,
            on_scan_success=on_scanner_success
        )

    def on_scanner_success(machine_data: dict):
        show_checklist_screen(machine_data)

    def show_checklist_screen(machine_data: dict):
        checklist_view(
            page=page,
            app_state=app_state,
            machine_data=machine_data,
            on_submit_success=on_checklist_submit_success,
            on_back=show_scanner_screen
        )

    def on_checklist_submit_success(submit_result: dict):
        show_success_screen(submit_result)

    def show_success_screen(submit_result: dict):
        success_view(
            page=page,
            app_state=app_state,
            submit_result=submit_result,
            on_done=show_scanner_screen
        )

    # Inicializa exibindo a tela de login
    show_login_screen()


if __name__ == "__main__":
    ft.app(target=main)
