"""
views/checklist_view.py — Tela de preenchimento do checklist de Entrada ou Saída.
Permite avaliar cada item da máquina como Conforme (Sim) ou Não Conforme (Não).
"""

import threading
import flet as ft
from config import COLORS


def checklist_view(page: ft.Page, app_state: dict, machine_data: dict, on_submit_success, on_back):
    """
    Renderiza a tela de preenchimento do checklist.

    Args:
        page: instância ft.Page do Flet.
        app_state: dict com 'api_client' e 'employee'.
        machine_data: dict retornado por get_machine contendo 'machine', 'checklist_type', 'checklist_items'.
        on_submit_success: callback(submit_result: dict) chamado após envio bem-sucedido.
        on_back: callback() chamado ao voltar para a tela anterior.
    """
    page.bgcolor = COLORS["bg_dark"]
    page.scroll = ft.ScrollMode.AUTO

    machine = machine_data.get("machine", {})
    checklist_type = machine_data.get("checklist_type", "entry")
    checklist_items = machine_data.get("checklist_items", [])

    machine_id = machine.get("id")
    machine_name = machine.get("name", "Máquina")
    machine_model = machine.get("model", "—")
    machine_sector = machine.get("sector", "—")

    type_label = "ENTRADA (Início do Turno)" if checklist_type == "entry" else "SAÍDA (Fim do Turno)"
    type_color = COLORS["primary"] if checklist_type == "entry" else COLORS["accent"]
    type_icon = ft.Icons.LOGIN if checklist_type == "entry" else ft.Icons.LOGOUT

    # Estado das respostas: dict {item_id: bool}
    # Por padrão, inicializamos todas as respostas como True (Conforme)
    responses = {item["id"]: True for item in checklist_items}

    # ── Widgets compartilhados ────────────────────────────────────────
    status_text = ft.Text(
        "",
        color=COLORS["danger"],
        size=13,
        text_align=ft.TextAlign.CENTER,
        visible=False,
    )

    submit_btn = ft.ElevatedButton(
        text="ENVIAR CHECKLIST",
        icon=ft.Icons.CHECK_CIRCLE,
        icon_color="#0f172a",
        bgcolor=COLORS["success"],
        color=COLORS["text"],
        height=52,
        expand=True,
        style=ft.ButtonStyle(
            shape=ft.RoundedRectangleBorder(radius=12),
            text_style=ft.TextStyle(weight=ft.FontWeight.BOLD, size=16),
        ),
    )

    loading_ring = ft.ProgressRing(
        color=COLORS["success"],
        width=28,
        height=28,
        stroke_width=3,
        visible=False,
    )

    # ── Handlers ─────────────────────────────────────────────────────
    def toggle_answer(item_id, val):
        responses[item_id] = val

    def submit_checklist(e):
        submit_btn.disabled = True
        loading_ring.visible = True
        status_text.visible = False
        page.update()

        def _do_submit():
            api = app_state["api_client"]
            result = api.submit_log(machine_id, checklist_type, responses)

            submit_btn.disabled = False
            loading_ring.visible = False

            if "error" in result:
                status_text.value = f"✖ {result['error']}"
                status_text.visible = True
                page.update()
                return

            # Sucesso
            page.update()
            on_submit_success(result)

        threading.Thread(target=_do_submit, daemon=True).start()

    submit_btn.on_click = submit_checklist

    # ── Componentes de UI ─────────────────────────────────────────────
    # Card: Informações da Máquina
    machine_card = ft.Container(
        content=ft.Column(
            [
                ft.Row(
                    [
                        ft.Icon(ft.Icons.PRECISION_MANUFACTURING, color=COLORS["accent"], size=22),
                        ft.Column(
                            [
                                ft.Text(machine_name, size=16, weight=ft.FontWeight.BOLD, color=COLORS["text"]),
                                ft.Text(f"Modelo: {machine_model}   |   Setor: {machine_sector}", size=12, color=COLORS["text_muted"]),
                            ],
                            spacing=2,
                        ),
                    ],
                    spacing=12,
                ),
            ]
        ),
        bgcolor=COLORS["bg_card"],
        border_radius=12,
        padding=16,
        margin=ft.margin.only(bottom=16),
        shadow=ft.BoxShadow(
            blur_radius=8,
            color=ft.Colors.with_opacity(0.2, "#000000"),
            offset=ft.Offset(0, 3),
        ),
    )

    # Lista de Itens do Checklist
    checklist_rows = []
    for item in checklist_items:
        item_id = item["id"]
        question_text = item["question"]

        # Switch customizado
        switch = ft.Switch(
            value=True,
            active_color=COLORS["success"],
            active_track_color=ft.Colors.with_opacity(0.3, COLORS["success"]),
            inactive_thumb_color=COLORS["danger"],
            inactive_track_color=ft.Colors.with_opacity(0.3, COLORS["danger"]),
            label="Conforme",
            label_position=ft.LabelPosition.LEFT,
            label_style=ft.TextStyle(size=12, color=COLORS["success"], weight=ft.FontWeight.BOLD),
        )

        def make_change_handler(i_id, sw):
            def on_change(e):
                val = sw.value
                toggle_answer(i_id, val)
                if val:
                    sw.label = "Conforme"
                    sw.label_style.color = COLORS["success"]
                else:
                    sw.label = "Irregular"
                    sw.label_style.color = COLORS["danger"]
                sw.update()
            return on_change

        switch.on_change = make_change_handler(item_id, switch)

        item_container = ft.Container(
            content=ft.Row(
                [
                    ft.Icon(ft.Icons.CHECKLIST, color=COLORS["text_muted"], size=18),
                    ft.Text(
                        question_text,
                        size=14,
                        color=COLORS["text"],
                        expand=True,
                        weight=ft.FontWeight.W_500,
                    ),
                    switch,
                ],
                alignment=ft.MainAxisAlignment.SPACE_BETWEEN,
                spacing=10,
            ),
            bgcolor=COLORS["bg_card"],
            border_radius=10,
            padding=16,
            border=ft.border.all(1, COLORS["bg_input"]),
        )
        checklist_rows.append(item_container)

    # Se não houver itens cadastrados para este checklist
    if not checklist_items:
        checklist_rows.append(
            ft.Container(
                content=ft.Column(
                    [
                        ft.Icon(ft.Icons.CHECKLIST_RTL, color=COLORS["text_muted"], size=40),
                        ft.Text(
                            "Nenhum item cadastrado para este tipo de checklist.",
                            color=COLORS["text_muted"],
                            size=14,
                            text_align=ft.TextAlign.CENTER,
                        ),
                    ],
                    horizontal_alignment=ft.CrossAxisAlignment.CENTER,
                    spacing=10,
                ),
                padding=30,
                alignment=ft.alignment.center,
            )
        )

    # ── Layout final ──────────────────────────────────────────────────
    page.controls.clear()
    page.controls.append(
        ft.Container(
            content=ft.Column(
                [
                    # AppBar customizada
                    ft.Container(
                        content=ft.Row(
                            [
                                ft.IconButton(
                                    icon=ft.Icons.ARROW_BACK,
                                    icon_color=COLORS["text"],
                                    on_click=lambda e: on_back(),
                                    tooltip="Voltar",
                                ),
                                ft.Text(
                                    "Preencher Checklist",
                                    size=18,
                                    weight=ft.FontWeight.BOLD,
                                    color=COLORS["text"],
                                ),
                                ft.Container(width=48),  # Placeholder para balancear o título
                            ],
                            alignment=ft.MainAxisAlignment.SPACE_BETWEEN,
                        ),
                        bgcolor=COLORS["bg_card"],
                        padding=ft.padding.symmetric(horizontal=10, vertical=8),
                    ),

                    # Título de Sessão / Tipo de Checklist
                    ft.Container(
                        content=ft.Row(
                            [
                                ft.Icon(type_icon, color=type_color, size=20),
                                ft.Text(
                                    type_label,
                                    size=14,
                                    weight=ft.FontWeight.BOLD,
                                    color=type_color,
                                    letter_spacing=0.5,
                                ),
                            ],
                            spacing=8,
                        ),
                        padding=ft.padding.symmetric(horizontal=20, vertical=12),
                    ),

                    # Conteúdo Principal
                    ft.Container(
                        content=ft.Column(
                            [
                                machine_card,
                                ft.Text(
                                    "Avaliação de Segurança",
                                    size=15,
                                    weight=ft.FontWeight.BOLD,
                                    color=COLORS["text"],
                                    margin=ft.margin.only(bottom=4),
                                ),
                                ft.Column(
                                    checklist_rows,
                                    spacing=10,
                                ),
                                ft.Container(height=10),
                                ft.Row([submit_btn]),
                                ft.Row([loading_ring], alignment=ft.MainAxisAlignment.CENTER),
                                ft.Container(
                                    content=status_text,
                                    alignment=ft.alignment.center,
                                    margin=ft.margin.only(top=10),
                                ),
                                ft.Container(height=30),  # Espaçamento inferior
                            ],
                            spacing=12,
                        ),
                        padding=ft.padding.symmetric(horizontal=16),
                        expand=True,
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
