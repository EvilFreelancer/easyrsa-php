<?php

namespace EasyRSA\Interfaces;

interface CommandsInterface
{
    /**
     * @return array<string>
     */
    public function initPKI(): array;

    /**
     * @param bool $nopass
     *
     * @return array<string>
     */
    public function buildCA(bool $nopass = false): array;

    /**
     * @return array<string>
     */
    public function genDH(): array;

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function genReq(string $name, bool $nopass = false): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function signReqServer(string $filename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function signReqClient(string $filename): array;

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function buildClientFull(string $name, bool $nopass = false): array;

    /**
     * @param string $name
     * @param bool   $nopass
     *
     * @return array<string>
     */
    public function buildServerFull(string $name, bool $nopass = false): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function revoke(string $filename): array;

    /**
     * @return array<string>
     */
    public function genCRL(): array;

    /**
     * @return array<string>
     */
    public function updateDB(): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function showReq(string $filename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function showCert(string $filename): array;

    /**
     * @param string $request_file_path
     * @param string $short_basename
     *
     * @return array<string>
     */
    public function importReq(string $request_file_path, string $short_basename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function exportP7(string $filename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function exportP12(string $filename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function setRSAPass(string $filename): array;

    /**
     * @param string $filename
     *
     * @return array<string>
     */
    public function setECPass(string $filename): array;
}
