<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // TABELA: members
        // ==========================================
        DB::statement("ALTER TABLE members COMMENT 'Membros da igreja'");
        DB::statement("ALTER TABLE members MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do membro'");
        DB::statement("ALTER TABLE members MODIFY COLUMN member_number INT NULL COMMENT 'Número do membro no rol'");
        DB::statement("ALTER TABLE members MODIFY COLUMN full_name VARCHAR(255) NULL COMMENT 'Nome completo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN gender VARCHAR(255) NULL COMMENT 'Gênero: masculino ou feminino'");
        DB::statement("ALTER TABLE members MODIFY COLUMN cpf VARCHAR(255) NULL COMMENT 'CPF do membro'");
        DB::statement("ALTER TABLE members MODIFY COLUMN middle_cpf VARCHAR(255) NULL COMMENT 'CPF parcial (meio) para busca'");
        DB::statement("ALTER TABLE members MODIFY COLUMN rg VARCHAR(255) NULL COMMENT 'RG do membro'");
        DB::statement("ALTER TABLE members MODIFY COLUMN work VARCHAR(255) NULL COMMENT 'Profissão'");
        DB::statement("ALTER TABLE members MODIFY COLUMN born_date VARCHAR(255) NULL COMMENT 'Data de nascimento formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE members MODIFY COLUMN email VARCHAR(255) NULL COMMENT 'E-mail'");
        DB::statement("ALTER TABLE members MODIFY COLUMN phone VARCHAR(255) NULL COMMENT 'Telefone fixo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN cell_phone VARCHAR(255) NULL COMMENT 'Celular'");
        DB::statement("ALTER TABLE members MODIFY COLUMN address VARCHAR(255) NULL COMMENT 'Endereço completo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN district VARCHAR(255) NULL COMMENT 'Bairro'");
        DB::statement("ALTER TABLE members MODIFY COLUMN city VARCHAR(255) NULL COMMENT 'Cidade'");
        DB::statement("ALTER TABLE members MODIFY COLUMN uf VARCHAR(255) NULL COMMENT 'Estado (UF)'");
        DB::statement("ALTER TABLE members MODIFY COLUMN marital_status VARCHAR(255) NULL COMMENT 'Estado civil: solteiro, casado, viuvo, divorciado'");
        DB::statement("ALTER TABLE members MODIFY COLUMN spouse VARCHAR(255) NULL COMMENT 'Nome do cônjuge'");
        DB::statement("ALTER TABLE members MODIFY COLUMN father VARCHAR(255) NULL COMMENT 'Nome do pai'");
        DB::statement("ALTER TABLE members MODIFY COLUMN mother VARCHAR(255) NULL COMMENT 'Nome da mãe'");
        DB::statement("ALTER TABLE members MODIFY COLUMN ecclesiastical_function VARCHAR(255) NULL COMMENT 'Função eclesiástica: pastor, diacono, presbitero, evangelista, missionario, obreiro'");
        DB::statement("ALTER TABLE members MODIFY COLUMN member_type VARCHAR(255) NULL COMMENT 'Tipo de membro: membro, congregado, visitante'");
        DB::statement("ALTER TABLE members MODIFY COLUMN ministries VARCHAR(255) NULL COMMENT 'Ministérios que participa'");
        DB::statement("ALTER TABLE members MODIFY COLUMN baptism_date VARCHAR(255) NULL COMMENT 'Data do batismo formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE members MODIFY COLUMN blood_type VARCHAR(255) NULL COMMENT 'Tipo sanguíneo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN education VARCHAR(255) NULL COMMENT 'Escolaridade'");
        DB::statement("ALTER TABLE members MODIFY COLUMN tithers_list TINYINT(1) NULL COMMENT '1=está na lista de dizimistas regulares'");
        DB::statement("ALTER TABLE members MODIFY COLUMN leader TINYINT(1) NULL COMMENT '1=é líder de algum grupo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN activated INT NOT NULL DEFAULT 1 COMMENT '1=membro ativo, 0=membro inativo'");
        DB::statement("ALTER TABLE members MODIFY COLUMN deleted INT NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");
        DB::statement("ALTER TABLE members MODIFY COLUMN group_ids JSON NULL COMMENT 'Array de IDs dos grupos que o membro participa'");
        DB::statement("ALTER TABLE members MODIFY COLUMN dependents_members_ids JSON NULL COMMENT 'Array de IDs dos membros dependentes'");

        // ==========================================
        // TABELA: entries
        // ==========================================
        DB::statement("ALTER TABLE entries COMMENT 'Entradas financeiras: dízimos, ofertas e designados'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID da entrada'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN member_id INT NULL COMMENT 'FK members.id - membro que contribuiu'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN account_id BIGINT UNSIGNED NULL COMMENT 'FK accounts.id - conta de destino'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN reviewer_id INT NULL COMMENT 'FK financial_reviewers.id - revisor responsável'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN cult_id INT NULL COMMENT 'FK cults.id - culto relacionado'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN group_returned_id INT NULL COMMENT 'FK ecclesiastical_divisions_groups.id - grupo que devolveu'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN group_received_id INT NULL COMMENT 'FK ecclesiastical_divisions_groups.id - grupo que recebeu'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN entry_type VARCHAR(255) NULL COMMENT 'Tipo: tithe=dízimo, offer=oferta, designated=designado'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN transaction_type VARCHAR(255) NULL COMMENT 'Forma de pagamento: pix, transfer, deposit, cash'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN transaction_compensation VARCHAR(255) NULL COMMENT 'Compensação da transação'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN date_transaction_compensation VARCHAR(255) NULL COMMENT 'Data da compensação formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN date_entry_register VARCHAR(255) NULL COMMENT 'Data do registro formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN amount DECIMAL(8,2) NULL COMMENT 'Valor em reais'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN recipient VARCHAR(255) NULL COMMENT 'Destinatário'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN devolution TINYINT(1) NULL COMMENT '1=é devolução de valor'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");
        DB::statement("ALTER TABLE entries MODIFY COLUMN comments VARCHAR(255) NULL COMMENT 'Observações'");

        // ==========================================
        // TABELA: exits
        // ==========================================
        DB::statement("ALTER TABLE exits COMMENT 'Saídas financeiras: despesas e pagamentos'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID da saída'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN account_id BIGINT UNSIGNED NULL COMMENT 'FK accounts.id - conta de origem'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN reviewer_id INT NULL COMMENT 'FK financial_reviewers.id - revisor responsável'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN exit_type VARCHAR(255) NULL COMMENT 'Tipo de saída'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN division_id INT NULL COMMENT 'FK ecclesiastical_divisions.id'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN group_id INT NULL COMMENT 'FK ecclesiastical_divisions_groups.id - grupo relacionado'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN payment_category_id INT NULL COMMENT 'FK payment_category.id - categoria do pagamento'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN payment_item_id INT NULL COMMENT 'FK payment_item.id - item do pagamento'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN is_payment TINYINT(1) NULL COMMENT '1=é pagamento de conta'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN transaction_type VARCHAR(255) NULL COMMENT 'Forma: pix, transfer, debit, cash, credit_card'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN transaction_compensation VARCHAR(255) NULL COMMENT 'Compensação da transação'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN date_transaction_compensation VARCHAR(255) NULL COMMENT 'Data da compensação formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN date_exit_register VARCHAR(255) NULL COMMENT 'Data do registro formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN amount DECIMAL(10,2) NULL COMMENT 'Valor em reais'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");
        DB::statement("ALTER TABLE exits MODIFY COLUMN comments VARCHAR(255) NULL COMMENT 'Observações'");

        // ==========================================
        // TABELA: cults
        // ==========================================
        DB::statement("ALTER TABLE cults COMMENT 'Cultos realizados'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do culto'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN reviewer_id INT NULL COMMENT 'FK financial_reviewers.id - revisor responsável'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN cult_day VARCHAR(255) NULL COMMENT 'Dia do culto: domingo, quarta, sexta, sabado'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN cult_date VARCHAR(255) NULL COMMENT 'Data do culto formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN date_transaction_compensation VARCHAR(255) NULL COMMENT 'Data da compensação formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN account_id BIGINT UNSIGNED NULL COMMENT 'FK accounts.id - conta relacionada'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN transaction_type VARCHAR(255) NULL COMMENT 'Tipo de transação'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN tithes_amount DECIMAL(8,2) NULL COMMENT 'Total de dízimos arrecadados no culto'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN designated_amount DECIMAL(8,2) NULL COMMENT 'Total de designados arrecadados no culto'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN offer_amount DECIMAL(8,2) NULL COMMENT 'Total de ofertas arrecadadas no culto'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN worship_without_entries TINYINT(1) NULL COMMENT '1=culto sem entradas registradas'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");
        DB::statement("ALTER TABLE cults MODIFY COLUMN comments VARCHAR(255) NULL COMMENT 'Observações'");

        // ==========================================
        // TABELA: accounts
        // ==========================================
        DB::statement("ALTER TABLE accounts COMMENT 'Contas bancárias da igreja'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID da conta'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN account_type VARCHAR(255) NULL COMMENT 'Tipo: checking=corrente, savings=poupança'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN bank_name VARCHAR(255) NULL COMMENT 'Nome do banco'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN agency_number VARCHAR(255) NULL COMMENT 'Número da agência'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN account_number VARCHAR(255) NULL COMMENT 'Número da conta'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN initial_balance DECIMAL(10,2) NULL COMMENT 'Saldo inicial em reais'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN initial_balance_date VARCHAR(255) NULL COMMENT 'Data do saldo inicial formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE accounts MODIFY COLUMN activated TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=conta ativa, 0=conta inativa'");

        // ==========================================
        // TABELA: accounts_balances
        // ==========================================
        DB::statement("ALTER TABLE accounts_balances COMMENT 'Saldos mensais das contas bancárias'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID do saldo'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN account_id BIGINT UNSIGNED NOT NULL COMMENT 'FK accounts.id'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN reference_date VARCHAR(255) NOT NULL COMMENT 'Mês de referência formato YYYY-MM'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN previous_month_balance DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Saldo do mês anterior em reais'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN current_month_balance DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Saldo atual do mês em reais'");
        DB::statement("ALTER TABLE accounts_balances MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: cards
        // ==========================================
        DB::statement("ALTER TABLE cards COMMENT 'Cartões de crédito da igreja'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID do cartão'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN name VARCHAR(255) NULL COMMENT 'Nome identificador do cartão'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN description TEXT NULL COMMENT 'Descrição do cartão'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN card_number VARCHAR(255) NULL COMMENT 'Últimos dígitos do cartão'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN credit_card_brand VARCHAR(255) NULL COMMENT 'Bandeira: visa, mastercard, elo, amex'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN `limit` DECIMAL(15,2) NULL COMMENT 'Limite do cartão em reais'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN due_day VARCHAR(255) NULL COMMENT 'Dia de vencimento da fatura'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN closing_day VARCHAR(255) NULL COMMENT 'Dia de fechamento da fatura'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=cartão ativo, 0=cartão inativo'");
        DB::statement("ALTER TABLE cards MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: cards_purchases
        // ==========================================
        DB::statement("ALTER TABLE cards_purchases COMMENT 'Compras realizadas no cartão de crédito'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID da compra'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN card_id BIGINT UNSIGNED NOT NULL COMMENT 'FK cards.id'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN establishment_name VARCHAR(255) NULL COMMENT 'Nome do estabelecimento'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN purchase_description VARCHAR(255) NULL COMMENT 'Descrição da compra'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN amount DECIMAL(10,2) NULL COMMENT 'Valor total da compra em reais'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN installments INT NULL COMMENT 'Número de parcelas'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN installment_amount DECIMAL(10,2) NULL COMMENT 'Valor de cada parcela em reais'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN date VARCHAR(255) NULL COMMENT 'Data da compra formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE cards_purchases MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: cards_invoices
        // ==========================================
        DB::statement("ALTER TABLE cards_invoices COMMENT 'Faturas dos cartões de crédito'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID da fatura'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN card_id BIGINT UNSIGNED NOT NULL COMMENT 'FK cards.id'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN status VARCHAR(255) NULL COMMENT 'Status: open=aberta, closed=fechada, paid=paga'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN amount DECIMAL(10,2) NULL COMMENT 'Valor total da fatura em reais'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN reference_date VARCHAR(255) NULL COMMENT 'Mês de referência formato YYYY-MM'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN payment_date VARCHAR(255) NULL COMMENT 'Data do pagamento formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN is_closed TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=fatura fechada'");
        DB::statement("ALTER TABLE cards_invoices MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: ecclesiastical_divisions_groups
        // ==========================================
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups COMMENT 'Grupos e ministérios da igreja'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do grupo'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN ecclesiastical_division_id INT NULL COMMENT 'FK ecclesiastical_divisions.id'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN parent_group_id INT NULL COMMENT 'FK para grupo pai (hierarquia)'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN leader_id INT NULL COMMENT 'FK members.id - líder do grupo'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN name VARCHAR(255) NULL COMMENT 'Nome do grupo ou ministério'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN description VARCHAR(255) NULL COMMENT 'Descrição do grupo'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN financial_group TINYINT(1) NULL COMMENT '1=é um grupo financeiro'");
        DB::statement("ALTER TABLE ecclesiastical_divisions_groups MODIFY COLUMN enabled TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=grupo ativo, 0=grupo inativo'");

        // ==========================================
        // TABELA: ecclesiastical_divisions
        // ==========================================
        DB::statement("ALTER TABLE ecclesiastical_divisions COMMENT 'Divisões eclesiásticas (categorias de grupos)'");
        DB::statement("ALTER TABLE ecclesiastical_divisions MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID da divisão'");
        DB::statement("ALTER TABLE ecclesiastical_divisions MODIFY COLUMN name VARCHAR(255) NULL COMMENT 'Nome da divisão'");
        DB::statement("ALTER TABLE ecclesiastical_divisions MODIFY COLUMN description VARCHAR(255) NULL COMMENT 'Descrição da divisão'");
        DB::statement("ALTER TABLE ecclesiastical_divisions MODIFY COLUMN enabled TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=divisão ativa, 0=divisão inativa'");

        // ==========================================
        // TABELA: payment_category
        // ==========================================
        DB::statement("ALTER TABLE payment_category COMMENT 'Categorias de pagamento para despesas'");
        DB::statement("ALTER TABLE payment_category MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID da categoria'");
        DB::statement("ALTER TABLE payment_category MODIFY COLUMN slug VARCHAR(255) NULL COMMENT 'Slug para identificação'");
        DB::statement("ALTER TABLE payment_category MODIFY COLUMN name VARCHAR(255) NULL COMMENT 'Nome da categoria'");
        DB::statement("ALTER TABLE payment_category MODIFY COLUMN description VARCHAR(255) NULL COMMENT 'Descrição da categoria'");

        // ==========================================
        // TABELA: payment_item
        // ==========================================
        DB::statement("ALTER TABLE payment_item COMMENT 'Itens de pagamento dentro de cada categoria'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do item'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN payment_category_id INT NULL COMMENT 'FK payment_category.id'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN slug VARCHAR(255) NULL COMMENT 'Slug para identificação'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN name VARCHAR(255) NULL COMMENT 'Nome do item'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN description VARCHAR(255) NULL COMMENT 'Descrição do item'");
        DB::statement("ALTER TABLE payment_item MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: movements
        // ==========================================
        DB::statement("ALTER TABLE movements COMMENT 'Movimentações financeiras dos grupos'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'ID da movimentação'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN group_id INT NULL COMMENT 'FK ecclesiastical_divisions_groups.id'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN entry_id INT NULL COMMENT 'FK entries.id - entrada relacionada'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN exit_id INT NULL COMMENT 'FK exits.id - saída relacionada'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN type ENUM('entry', 'exit') NULL COMMENT 'Tipo: entry=entrada, exit=saída'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN sub_type VARCHAR(255) NULL COMMENT 'Subtipo da movimentação'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN amount DECIMAL(10,2) NULL COMMENT 'Valor da movimentação em reais'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN balance DECIMAL(10,2) NULL COMMENT 'Saldo do grupo após a movimentação'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN movement_date VARCHAR(255) NULL COMMENT 'Data da movimentação formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE movements MODIFY COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: financial_reviewers
        // ==========================================
        DB::statement("ALTER TABLE financial_reviewers COMMENT 'Revisores financeiros (tesoureiros)'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do revisor'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN full_name VARCHAR(255) NULL COMMENT 'Nome completo'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN reviewer_type VARCHAR(255) NULL COMMENT 'Tipo de revisor'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN gender VARCHAR(255) NULL COMMENT 'Gênero: masculino ou feminino'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN cpf VARCHAR(255) NULL COMMENT 'CPF'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN email VARCHAR(255) NULL COMMENT 'E-mail'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN cell_phone VARCHAR(255) NULL COMMENT 'Celular'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN activated INT NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo'");
        DB::statement("ALTER TABLE financial_reviewers MODIFY COLUMN deleted INT NOT NULL DEFAULT 0 COMMENT '1=registro deletado (soft delete)'");

        // ==========================================
        // TABELA: consolidation_entries
        // ==========================================
        DB::statement("ALTER TABLE consolidation_entries COMMENT 'Consolidação diária de entradas financeiras'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID da consolidação'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN date VARCHAR(255) NULL COMMENT 'Data da consolidação formato YYYY-MM-DD'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN consolidated TINYINT(1) NULL COMMENT '1=consolidado'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN designated_amount DECIMAL(8,2) NULL COMMENT 'Total de designados do dia'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN offers_amount DECIMAL(8,2) NULL COMMENT 'Total de ofertas do dia'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN tithe_amount DECIMAL(8,2) NULL COMMENT 'Total de dízimos do dia'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN total_amount DECIMAL(8,2) NULL COMMENT 'Total geral do dia'");
        DB::statement("ALTER TABLE consolidation_entries MODIFY COLUMN monthly_target DECIMAL(8,2) NULL COMMENT 'Meta mensal em reais'");

        // ==========================================
        // TABELA: users
        // ==========================================
        DB::statement("ALTER TABLE users COMMENT 'Usuários do sistema'");
        DB::statement("ALTER TABLE users MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do usuário'");
        DB::statement("ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NOT NULL COMMENT 'E-mail de login'");
        DB::statement("ALTER TABLE users MODIFY COLUMN activated INT NOT NULL DEFAULT 1 COMMENT '1=usuário ativo, 0=usuário inativo'");
        DB::statement("ALTER TABLE users MODIFY COLUMN type VARCHAR(255) NULL COMMENT 'Tipo de usuário'");

        // ==========================================
        // TABELA: user_details
        // ==========================================
        DB::statement("ALTER TABLE user_details COMMENT 'Detalhes dos usuários do sistema'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID do detalhe'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN user_id INT NOT NULL COMMENT 'FK users.id'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN full_name VARCHAR(255) NULL COMMENT 'Nome completo'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN gender VARCHAR(255) NULL COMMENT 'Gênero: masculino ou feminino'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN phone VARCHAR(255) NULL COMMENT 'Telefone'");
        DB::statement("ALTER TABLE user_details MODIFY COLUMN city VARCHAR(255) NULL COMMENT 'Cidade'");
    }

    public function down(): void
    {
        // Os comentários não precisam ser removidos no rollback
        // pois não afetam a estrutura do banco
    }
};
