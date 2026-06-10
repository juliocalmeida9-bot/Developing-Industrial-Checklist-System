"""
views/success_view.py — Tela de feedback pós-envio do checklist.
Mostra se o acesso foi aprovado ou se há restrições (reprovado/bloqueado) devido a itens não conformes.
"""

import flet as ft
from config import COLORS


def success_view(page: ft.Page, app_state: dict, submit_result: dict, on_done):
    """
    Renderiza a tela de sucesso ou erro (bloqueio) pós-checklist.

    Args:
        page: instância ft.Page do Flet.
        app_state: dict global.
        submit_result: dict retornado pela API contendo 'status', 'message', 'log_id'.
        on_done: callback() chamado ao clicar no botão para voltar ao início.
    """
    page.bgcolor = COLORS["bg_dark"]
    page.scroll = ft.ScrollMode.AUTO

    status = submit_result.get("status", "approved")
    message = submit_result.get("message", "Operação concluída com sucesso!")

    is_approved = status == "approved"

    # Definição visual conforme o status
    if is_approved:
        icon_name = ft.Icons.CHECK_CIRCLE_ROUNDED
        icon_color = COLORS["success"]
        title = "Operação Liberada!"
        subtitle = "A máquina está em conformidade com as regras de segurança."
        accent_bg = ft.Colors.with_opacity(0.1, COLORS["success"])
    else:
        icon_name = ft.Icons.WARNING_AMBER_ROUNDED
        icon_color = COLORS["danger"]
        title = "Atenção: Acesso Restrito!"
        subtitle = "Foram identificados itens fora de conformidade."
        accent_bg = ft.Colors.with_opacity(0.1, COLORS["danger"])

    # ── Componentes de UI ─────────────────────────────────────────────

    # Círculo de Status Animado/Estilizado
    status_icon_container = ft.Container(
        content=ft.Icon(
            name=icon_name,
            size=72,
            color=icon_color,
        ),
        bgcolor=accent_bg,
        shape=ft.BoxShape.CIRCLE,
        width=120,
        height=120,
        alignment=ft.alignment.center,
        margin=ft.margin.only(bottom=20),
    )

    # Card informativo principal
    info_card = ft.Container(
        content=ft.Column(
            [
                ft.Text(
                    title,
                    size=22,
                    weight=ft.FontWeight.BOLD,
                    color=COLORS["text"],
                    text_align=ft.TextAlign.CENTER,
                ),
                ft.Container(height=4),
                ft.Text(
                    subtitle,
                    size=14,
                    color=COLORS["text_muted"],
                    text_align=ft.TextAlign.CENTER,
                ),
                ft.Divider(color=COLORS["bg_input"], thickness=1, height=24),
                ft.Container(
                    content=ft.Text(
                        message,
                        size=14,
                        color=icon_color,
                        weight=ft.FontWeight.BOLD,
                        text_align=ft.TextAlign.CENTER,
                    ),
                    bgcolor=accent_bg,
                    border_radius=8,
                    padding=12,
                    alignment=ft.alignment.center,
                ),
            ],
            horizontal_alignment=ft.CrossAxisAlignment.CENTER,
            spacing=10,
        ),
        bgcolor=COLORS["bg_card"],
        border_radius=16,
        padding=24,
        shadow=ft.BoxShadow(
            blur_radius=12,
            color=ft.Colors.with_opacity(0.3, "#000000"),
            offset=ft.Offset(0, 4),
        ),
    )

    # Recomendações e Instruções de Segurança adicionais
    recommendations = []
    if is_approved:
        recommendations = [
            "1. Utilize todos os EPIs necessários.",
            "2. Mantenha a área de trabalho limpa.",
            "3. Em caso de ruído anormal, desligue o motor.",
        ]
    else:
        recommendations = [
            "⚠️ NÃO LIGUE a máquina sob nenhuma hipótese.",
            "🔧 Notifique imediatamente o supervisor de área ou a manutenção.",
            "📝 Registre as falhas detalhadamente se solicitado.",
        ]

    rec_list = []
    for r in recommendations:
        rec_list.append(
            ft.Text(
                r,
                size=13,
                color=COLORS["danger"] if not is_approved else COLORS["text_muted"],
                weight=ft.FontWeight.W_500 if not is_approved else ft.FontWeight.NORMAL,
            )
        )

    recommendations_card = ft.Container(
        content=ft.Column(
            [
                ft.Text(
                    "Procedimento Operacional:" if is_approved else "Ações Obrigatórias:",
                    size=14,
                    weight=ft.FontWeight.BOLD,
                    color=COLORS["accent"],
                ),
                ft.Container(height=4),
                ft.Column(rec_list, spacing=6),
            ],
            spacing=6,
        ),
        bgcolor=COLORS["bg_card"],
        border_radius=12,
        padding=20,
        border=ft.border.all(1, COLORS["bg_input"]),
        margin=ft.margin.only(top=16),
    )

    # Botão de retorno
    done_btn = ft.ElevatedButton(
        text="VOLTAR AO INÍCIO",
        icon=ft.Icons.HOME_ROUNDED,
        bgcolor=COLORS["primary"],
        color=COLORS["text"],
        height=48,
        expand=True,
        style=ft.ButtonStyle(
            shape=ft.RoundedRectangleBorder(radius=10),
            text_style=ft.TextStyle(weight=ft.FontWeight.BOLD, size=15),
        ),
        on_click=lambda e: on_done(),
    )

    # ── Layout final ──────────────────────────────────────────────────
    page.controls.clear()
    page.controls.append(
        ft.Container(
            content=ft.Column(
                [
                    ft.Container(height=30),  # Espaço superior
                    status_icon_container,
                    info_card,
                    recommendations_card,
                    ft.Container(height=30),
                    ft.Row([done_btn]),
                    ft.Container(height=30),  # Margem inferior
                ],
                horizontal_alignment=ft.CrossAxisAlignment.CENTER,
                spacing=10,
                scroll=ft.ScrollMode.AUTO,
            ),
            bgcolor=COLORS["bg_dark"],
            padding=ft.padding.symmetric(horizontal=20),
            expand=True,
        )
    )
    page.update()
